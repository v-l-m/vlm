#include <stdio.h>
#include <math.h>

/*
 * w. ebisuzaki
 *
 *  return x**y
 *
 *
 *  input: double x
 *	   int y
 */
double old_int_power(double x, int y) {

	double value;

	if (y < 0) {
		y = -y;
		x = 1.0 / x;
	}
	value = 1.0;

	while (y) {
		if (y & 1) {
			value *= x;
		}
		x = x * x;
		y >>= 1;
	}
	return value;
}

/* more efficient this way... */
double inline int_power(double x, int y) {
  return pow(x, y);
}
