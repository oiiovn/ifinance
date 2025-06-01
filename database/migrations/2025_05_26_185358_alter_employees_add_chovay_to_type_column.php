<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterEmployeesAddChovayToTypeColumn extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE employees MODIFY COLUMN type ENUM('employee', 'shop', 'dautu', 'nhacungcap', 'chovay') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE employees MODIFY COLUMN type ENUM('employee', 'shop', 'dautu', 'nhacungcap') NOT NULL");
    }
}
