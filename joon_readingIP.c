#include <pcap.h>
#include <stdio.h>
#include <arpa/inet.h>
#include <stdlib.h>
#include <string.h>
#include <mariadb/mysql.h>

#define ARP_REQUEST 1
#define ARP_REPLY 2

typedef struct ethhdr {
	u_char ether_dest_addr[6];
	u_char ether_src_addr[6];
	u_int16_t ether_type;
} ethhdr_t;

typedef struct arphdr {
	u_int16_t htype;	// Hardware Type
	u_int16_t ptype;	// Protocol Type
	u_char hlen;		// Hardware Address Length
	u_char plen;		// Protocol Address Length
	u_int16_t oper;		// Operation Code
	u_char sha[6];		// Sender Hardware Address
	u_char spa[4];		// Sender IP Address
	u_char tha[6];		// Target Hardware Address
	u_char tpa[4];		//Target IP Address
} arphdr_t;

#define MAXBYTES2CAPTURE 2048

char* itoa(int i, char* buf) {
	memset(buf, 0, 255);
	sprintf(buf, "%d", i);
	return buf;
}

char* xtoa(int i, char* buf) {
	memset(buf, 0, 255);
	sprintf(buf, "%02X", i);
	return buf;
}

int main(int argc, char *argv[]) {
	int i=0;
	bpf_u_int32 netaddr=0, mask=0;
	struct bpf_program filter;
	char errbuf[PCAP_ERRBUF_SIZE];
	pcap_t *descr = NULL;
	struct pcap_pkthdr pkthdr;
	const unsigned char *packet = NULL;
	unsigned char reply_packet[42];
	arphdr_t *arpheader = NULL;
	memset(errbuf, 0, PCAP_ERRBUF_SIZE);

	char Sender_IP[255];
	char Sender_MAC[255];
	char Target_IP[255];
	char Target_MAC[255];
	char buffer[255];
	char* token;
	char query_sentence[255];

	MYSQL *conn;
	MYSQL_RES *res;
	MYSQL_ROW row;
	int r;

	char *server = "localhost";
	char *user = "cswin";
	char *password = "cswin";
	char *database = "berryIP";

	conn = mysql_init(NULL);

	if (!mysql_real_connect(conn, server, user, password, database, 0, NULL, 0)) {
		fprintf(stderr, "%s\n", mysql_error(conn));
		exit(1);
	}

	res = mysql_use_result(conn);

	if (argc != 2) {
		printf("USAGE: arpsniffer <interface>\n");
		exit(1);
	}

	if ((descr = pcap_open_live(argv[1], MAXBYTES2CAPTURE, 0, 512, errbuf)) == NULL) {
		fprintf(stderr, "ERROR: %s\n", errbuf);
		exit(1);
	}

	if (pcap_lookupnet(argv[1], &netaddr, &mask, errbuf) == -1) {
		fprintf(stderr, "ERROR: %s\n", errbuf);
		exit(1);
	}

	if (pcap_compile(descr, &filter, "arp", 1, mask) == -1) {
		fprintf(stderr, "ERROR: %s\n", pcap_geterr(descr));
		exit(1);
	}

	if (pcap_setfilter(descr, &filter) == -1) {
		fprintf(stderr, "ERROR: %s\n", pcap_geterr(descr));
		exit(1);
	}

	memset(reply_packet, 0, sizeof(reply_packet));
	reply_packet[12] = 0x08;
	reply_packet[13] = 0x06;
	reply_packet[14] = 0x00;
	reply_packet[15] = 0x01;
	reply_packet[16] = 0x08;
	reply_packet[17] = 0x00;
	reply_packet[18] = 0x06;
	reply_packet[19] = 0x04;
	reply_packet[20] = 0x00;
	reply_packet[21] = 0x02;

	while(1) {
		if ((packet = pcap_next(descr, &pkthdr)) == NULL) {
			fprintf(stderr, "ERROR: Error getting the packet. \n", errbuf);
			exit(1);
		}

		arpheader = (struct arphdr *)(packet+14);

		if (ntohs(arpheader->htype) == 1 && ntohs(arpheader->ptype) == 0x0800) {
			memset(Sender_IP, 0, 255);
			memset(Sender_MAC, 0, 255);
			memset(Target_IP, 0, 255);
			memset(Target_MAC, 0, 255);

			for (i=0; i<3; i++) {
				strcat(Sender_IP, itoa(arpheader->spa[i], buffer));
				strcat(Sender_IP, ".");
			}
			strcat(Sender_IP, itoa(arpheader->spa[3], buffer));
			strcat(Sender_IP, "\0");

			for (i=0; i<5; i++) {
				strcat(Sender_MAC, xtoa(arpheader->sha[i], buffer));
				strcat(Sender_MAC, ":");
			}
			strcat(Sender_MAC, xtoa(arpheader->sha[5], buffer));
			strcat(Sender_MAC, "\0");

			for (i=0; i<3; i++) {
				strcat(Target_IP, itoa(arpheader->tpa[i], buffer));
				strcat(Target_IP, ".");
			}
			strcat(Target_IP, itoa(arpheader->tpa[3], buffer));
			strcat(Target_IP, "\0");

			for (i=0; i<5; i++) {
				strcat(Target_MAC, xtoa(arpheader->tha[i], buffer));
				strcat(Target_MAC, ":");
			}
			strcat(Target_MAC, xtoa(arpheader->tha[5], buffer));
			strcat(Target_MAC, "\0");
			
			if (strcmp(Sender_IP, Target_IP) == 0) {

				printf("\n\nReceived Packet Size: %d bytes\n", pkthdr.len);
				printf("Hardware Type: %s\n", (ntohs(arpheader->htype) == 1) ? "Ethernet" : "Unknown");
				printf("Protocol Type: %s\n", (ntohs(arpheader->ptype) == 0x0800) ? "IPv4" : "Unknown");
				printf("Operation: %s\n", (ntohs(arpheader->oper) == ARP_REQUEST) ? "ARP Request" : "ARP Reply");

				printf("Sender MAC: ");

				for (i=0; i<5; i++)
					printf("%02X:", arpheader->sha[i]);
				printf("%02X", arpheader->sha[5]);

				printf("\nSender IP: ");

				for (i=0; i<3; i++)
					printf("%d.", arpheader->spa[i]);
				printf("%d", arpheader->spa[3]);

				printf("\nTarget MAC: ");

				for (i=0; i<5; i++)
					printf("%02X:", arpheader->tha[i]);
				printf("%02X", arpheader->tha[5]);

				printf("\nTarget IP: ");

				for (i=0; i<3; i++)
					printf("%d.", arpheader->tpa[i]);
				printf("%d", arpheader->tpa[3]);

				printf("\n");
				
				memset(query_sentence, 0, 255);
				strcat(query_sentence, "select * from IP_INFO where IS_PROTECTED = 1 and IP_ADDR = \"");
				strcat(query_sentence, Sender_IP);
				strcat(query_sentence, "\"");
				
				mysql_query(conn, query_sentence);
				res = mysql_store_result(conn);

				mysql_free_result(res);

				if ((row = mysql_fetch_row(res)) != NULL && (ntohs(arpheader->oper) == ARP_REQUEST)) {
					for (i=0; i<6; i++) {
						reply_packet[i] = arpheader->sha[i];
					}

					strcpy(buffer, row[1]);
					token = strtok(buffer, ":");
					for (i=6; token != NULL; i++) {
						reply_packet[i] = strtol(token, NULL, 16);
						reply_packet[i+16] = reply_packet[i];
						token = strtok(NULL, ":");
					}

					for (i=0; i<4; i++) {
						reply_packet[i+28] = arpheader->tpa[i];
					}

					for (i=0; i<6; i++) {
						reply_packet[i+32] = arpheader->sha[i];
					}

					for (i=0; i<4; i++) {
						reply_packet[i+38] = arpheader->spa[i];
					}

					if (pcap_sendpacket(descr, reply_packet, sizeof(reply_packet)) != 0) {
						printf("ARP Reply Send Error\n");
					}
					else {
						printf("ARP Reply Send Succeed\n");
					}
				}
				else {
					memset(query_sentence, 0, 255);
					sprintf(query_sentence, "insert into IP_INFO(IP_ADDR, MAC_ADDR, OWNER, IS_OK, IS_PROTECTED, IP_REMARK) values (\'%s\', \'%s\', \'%s\', 0, 0, NULL)", Sender_IP, Sender_MAC, Sender_MAC);
					
					if ((r = mysql_query(conn, query_sentence)) != 0) {
						printf("IP Information Insert Succeed\n");
					}
					else {
						printf("IP Information Insert Failed\n");
					}
				}
			}
		}
	}

	return 0;
}
