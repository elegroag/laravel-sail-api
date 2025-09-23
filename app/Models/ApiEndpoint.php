<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiEndpoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_name',
        'endpoint_name',
        'connection_name',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_endpoints';

    /**
     * Get endpoint by service and endpoint name.
     *
     * @param string $serviceName
     * @param string $endpointName
     * @return ApiEndpoint|null
     */
    public static function getEndpoint($serviceName, $endpointName)
    {
        return self::where('service_name', $serviceName)
            ->where('endpoint_name', $endpointName)
            ->first();
    }

    /**
     * Update or create an endpoint.
     *
     * @param string $serviceName
     * @param string $endpointName
     * @param string|null $connectionName
     * @return ApiEndpoint
     */
    public static function updateOrCreateEndpoint($serviceName, $endpointName, $connectionName = null)
    {
        return self::updateOrCreate(
            ['service_name' => $serviceName, 'endpoint_name' => $endpointName],
            ['connection_name' => $connectionName]
        );
    }
}
