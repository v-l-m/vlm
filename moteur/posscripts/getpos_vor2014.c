#include <stdio.h>
#include <string.h>

//#define _CRT_SECURE_NO_WARNINGS (1)
//#pragma warning(disable:4996) 

#define BOAT_ID_ALVI (-2200)
#define BOAT_ID_ADOR (-2201)
#define BOAT_ID_DFRT (-2202)
#define BOAT_ID_TBRU (-2203)
#define BOAT_ID_VEST (-2204)
#define BOAT_ID_SCA1 (-2205)
#define BOAT_ID_MAPF (-2206)


int GetBoatId(char * Data)
{
	if (!strncmp( Data, "ALVI",4))
	{
		return BOAT_ID_ALVI;
	}
	
	if (!strncmp( Data, "ADOR",4))
	{
		return BOAT_ID_ADOR;
	}

	if (!strncmp( Data, "DFRT",4))
	{
		return BOAT_ID_DFRT;
	}

	if (!strncmp( Data, "TBRU",4))
	{
		return BOAT_ID_TBRU;
	}

	if (!strncmp( Data, "SCA1",4))
	{
		return BOAT_ID_SCA1;
	}

	if (!strncmp( Data, "VEST",4))
	{
		return BOAT_ID_VEST;
	}
	
	if (!strncmp( Data, "MAPF",4))
	{
		return BOAT_ID_MAPF;
	}
	printf("%s\n",Data);
	return 0;
}

void main(int argc, char *argv[])
{
	int RaceID = -1;
	FILE *f = fopen("./Res.Bin","r");
	//FILE *fEpoch = fopen("./LastEpoch","r");
	int LastEpoch=0;

	// Check argcount and get raceid if one only
	if (argc == 2)
	{
		sscanf(argv[1],"%d",&RaceID);
	}
	else
	{
		printf("Invalid arg count. Usage getpos_vor2014 <RaceId>\n");
		return;
	}
	//fscanf (fEpoch,"%d",&LastEpoch);
	if (f)
	{
		char DataLine[0x47];
		int RowCount = 0;

//printf("LastEpoch %d\n",LastEpoch);
		while (fread(DataLine,sizeof(char),0x47,f))
		{
			int Epoch = *(int*) (DataLine+14);
			float Lon = *(float*) (DataLine+6);
			float Lat = *(float*) (DataLine+10);
			int BoatId = GetBoatId(DataLine);

			//	printf("%d|%d|%f|%f\n",BoatId,Epoch,Lon,Lat);
			if (BoatId && (Epoch > LastEpoch))
			{
//20091108|1|1257681600|-729|BT|Sï¿œbastien Josse - Jean Franï¿œois Cuzon|50.016000|-1.891500|85.252725|4651.600000
				printf("%d|1|%d|%d| | |%f|%f|0|0\n",RaceID,Epoch,BoatId,Lon,Lat);
			}
//			else
//			{
//				printf("%d %d %d\n",Epoch,LastEpoch,Epoch > LastEpoch);
//			}
			RowCount ++;	
		}
//printf("RowCount %d",RowCount);
	}
	else
	{
		printf("File Not Opened!!!\n");
	}
	return ;
}




