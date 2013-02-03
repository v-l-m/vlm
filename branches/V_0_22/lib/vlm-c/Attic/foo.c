#include <math.h>
#include <stdio.h>

#define degToRad(angle) ((angle/180.0) * M_PI)

#define radToDeg(angle) ((angle * 180.0) / M_PI)

int main (int argc, char **argv) {
  /* depart Horn -> arivee Le Cap */
  /* start */
  double latHorn = -56.200; 
  double longHorn  = 66.900;
  /* arrival */
  double latLeCap = -34.367;
  double longLeCap = -18.383;

  double longitude, latitude, speed, time_vac;
  double gprime, d, m0, radLatStart, radLatArrival;
  double initialAngle, denom, t_lat, t_long;
  double lambda, lambdaStart, lambdaArrival;
  int i;

  printf("Orthodromy\n");

  gprime = longLeCap - longHorn;
  if (gprime <= -180.0) {
    gprime += 360.0;
  } else if (gprime > 180.0) {
    gprime -= 360.0;
  }
  printf("diff longitude %.3f\n", gprime);
  printf("Going %s\n", (gprime<0) ? "east" : "west");
  radLatStart = degToRad(latHorn);
  radLatArrival = degToRad(latLeCap);
  d = acos(sin(radLatArrival) * sin(radLatStart) +
	   cos(radLatArrival) * cos(radLatStart) * cos(degToRad(gprime)));
  printf("Angle is: %.2f\n", radToDeg(d));
  m0 = 60 * radToDeg(d);
  printf("Distance is %.2f nautic miles\n", m0);
  denom = cos(radLatStart) * sin(d);
  if (denom == 0) {
    printf("Going vertical !\n");
    initialAngle = (radLatStart<radLatArrival) ? M_PI / 2 : -M_PI / 2;
  } else {
    initialAngle = acos((sin(radLatArrival)-sin(radLatStart)*cos(d)) / denom);
  }
  if (gprime >0) {
    initialAngle = (2 * M_PI) - initialAngle;
  }
  printf("Initial Angle: %2.3f\n", radToDeg(initialAngle));

  printf("Loxodromy: \n");
  time_vac = 600; // 10mn
  speed = 17; // 17 kts
  latitude = latHorn + (speed * (time_vac / 3600) / 60) * cos(initialAngle);
  printf("Latitude after %.0fs: %.3f\n", time_vac, latitude);
  t_lat = (latHorn + latitude) / 2.0;
  // approx.
  longitude = longHorn - ((speed*(time_vac/3600))*sin(initialAngle)  /
			  (60 * cos(degToRad(t_lat))) );
  printf("Longitude after %.0fs: %.12g\n", time_vac, longitude);
  // exact
  lambdaStart   = (180 / M_PI) * log(tan(M_PI/4 + degToRad(latHorn)/2));
  lambdaArrival = (180 / M_PI) * log(tan(M_PI/4 + degToRad(latitude)/2));
  longitude = longHorn - (lambdaArrival-lambdaStart) * tan(initialAngle);
  printf("Longitude after %.0fs: %.12g\n", time_vac, longitude);

  return 0;
}
