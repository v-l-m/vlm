/**
 * $Id: lines.h,v 1.10 2011-04-07 21:03:35 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *
 *      See COPYING file for copying and redistribution conditions.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Contact: <yves@raubacapeu.net>
 */

#ifndef _VLMC_LINES_H_
#define _VLMC_LINES_H_

#include "defs.h"
#include "types.h"

/**
 * All latitude/longitude in rad
 * latitude/longitude of segment 1, latitude/longitude of segment 2
 * latitude/longitude addresses of intersection point (99% of seg1-A -> seg1-B)
 * @returns a double, between MIN_LIMIT and MAX_LIMIT when an intersection
 * occurs, relative to segment 1; -1 otherwise
 */
double intersects PARAM10(double, double, double, double,
			  double, double, double, double,
			  double *, double *);

/**
 * All latitude/longitude in rad
 * latitude/longitude of segment 1, latitude/longitude of segment 2
 * latitude/longitude addresses of intersection point (99% of seg1-A -> seg1-B)
 * @returns an integer, 1 if an intersction occurs, 0 otherwise
 */
int check_intersects PARAM8(double, double, double, double,
			    double, double, double, double);

#ifdef PARANOID_COAST_CHECK
/**
 * All latitude/longitude in rad
 * latitude/longitude of segment 1, latitude/longitude of segment 2
 * latitude/longitude addresses of intersection point (99% of seg1-A -> seg1-B)
 * @returns a double, between MIN_LIMIT and MAX_LIMIT when an intersection
 * occurs, relative to segment 1; -1 otherwise
 */
double paranoid_intersects PARAM10(double, double, double, double,
				   double, double, double, double,
				   double *, double *);
#endif /* PARANOID_COAST_CHECK */

/**
 * All latitude/longitude in rad
 * latitude/longitude of point A, latitude/longitude of segment 1
 * @returns a double, the distance in nm from point A to segment 1
 */
double distance_to_line PARAM6(double, double, double, double,
			       double, double);

/**
 * All latitude/longitude in rad
 * latitude/longitude of point A, latitude/longitude of segment 1
 * @returns a double, the distance in nm from point A to segment 1
 * The last parameter is filled with the position from segpoint 1 to 2
 * as a value between 0 and 1 (0 is point 1, 1 is point 2)
 */
double distance_to_line_ratio PARAM7(double, double, double, double,
				     double, double, double *);

/**
 * All latitude/longitude in rad
 * latitude/longitude of point A, latitude/longitude of segment 1
 * @returns a double, the distance in nm from point A to segment 1
 * The last parameter is filled with the position from segpoint 1 to 2
 * as a value between 0 and 1 (0 is point 1, 1 is point 2)
 */
double distance_to_line_ratio_xing PARAM9(double, double, double, double,
					  double, double, 
					  double *, double *,
					  double *);

/**
 * All latitude/longitude in rad
 * latitude/longitude of segment 1, latitude/longitude of segment 2
 * latitude/longitude addresses of intersection point (99% of seg1-A -> seg1-B)
 * @returns a double, between MIN_LIMIT and MAX_LIMIT when an intersection
 * occurs with the cast line, -1 otherwise
 */
double check_coast PARAM6(double, double, double, double,
			  double *, double *);

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate. 
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment, and pointers to lat/long of closest point
 */
double distance_to_line_dichotomy_xing PARAM8(double, double,
					      double, double,
					      double, double,
					      double *, double *);

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate.
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment
 */
double distance_to_line_dichotomy PARAM6(double, double,
					 double, double,
					 double, double);

#endif /* _VLMC_LINES_H_ */
