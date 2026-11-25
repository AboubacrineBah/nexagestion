<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_document_lines', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_document_lines', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->index(['sales_document_id', 'product_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_document_lines', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_document_lines', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });
    }
};
