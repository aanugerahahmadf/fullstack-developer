<?php

namespace App\Utils;

class CoordinateConverter
{
    /**
     * Convert decimal degrees to Degrees, Minutes, Seconds format
     *
     * @param float $decimalDegrees
     * @param bool $isLatitude
     * @return string
     */
    public static function decimalToDMS(float $decimalDegrees, bool $isLatitude = true): string
    {
        $absolute = abs($decimalDegrees);
        $degrees = floor($absolute);
        $minutes = fmod($absolute * 60, 60);
        $seconds = fmod($minutes * 60, 60);
        $minutes = floor($minutes);
        
        $hemisphere = '';
        if ($isLatitude) {
            $hemisphere = $decimalDegrees >= 0 ? 'N' : 'S';
        } else {
            $hemisphere = $decimalDegrees >= 0 ? 'E' : 'W';
        }
        
        return sprintf('%d° %d′ %.2f″ %s', $degrees, $minutes, $seconds, $hemisphere);
    }
    
    /**
     * Convert Degrees, Minutes, Seconds to decimal degrees
     *
     * @param int $degrees
     * @param int $minutes
     * @param float $seconds
     * @param string $hemisphere
     * @return float
     */
    public static function dmsToDecimal(int $degrees, int $minutes, float $seconds, string $hemisphere): float
    {
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        if (in_array(strtoupper($hemisphere), ['S', 'W'])) {
            $decimal *= -1;
        }
        
        return $decimal;
    }
    
    /**
     * Validate coordinate ranges
     *
     * @param float|null $latitude
     * @param float|null $longitude
     * @return array
     */
    public static function validateCoordinates(?float $latitude, ?float $longitude): array
    {
        $errors = [];
        
        if ($latitude !== null) {
            if ($latitude < -90 || $latitude > 90) {
                $errors['latitude'] = 'Latitude must be between -90 and 90 degrees';
            }
        }
        
        if ($longitude !== null) {
            if ($longitude < -180 || $longitude > 180) {
                $errors['longitude'] = 'Longitude must be between -180 and 180 degrees';
            }
        }
        
        return $errors;
    }
    
    /**
     * Format coordinate for display with specified precision
     *
     * @param float|null $coordinate
     * @param int $precision
     * @return string
     */
    public static function formatCoordinate(?float $coordinate, int $precision = 8): string
    {
        if ($coordinate === null) {
            return 'N/A';
        }
        
        return number_format($coordinate, $precision, '.', '');
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth radius in kilometers
        
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}