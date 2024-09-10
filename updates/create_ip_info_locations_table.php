<?php namespace Raccoon\GeoLocation\Updates;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateIpInfoLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('raccoon_geolocation_ip_info_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('ip', 50);
            $table->string('country_code', 2)->nullable();
            $table->string('country', 30)->nullable();
            $table->string('state', 40)->nullable();
            $table->string('city', 40)->nullable();
            $table->string('zip', 20)->nullable();
            $table->decimal('latitude', 8, 5)->default(0);
            $table->decimal('longitude', 8, 5)->default(0);
            $table->string('timezone', 10)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();

            $table->primary('ip');
        });
    }

    public function down()
    {
        Schema::dropIfExists('raccoon_geolocation_ip_info_locations');
    }
}
