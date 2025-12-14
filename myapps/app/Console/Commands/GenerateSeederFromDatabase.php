<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateSeederFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-seeder {table?} {--model=} {--class=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeder file from existing database table data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            return $this->generateAllSeeders();
        }

        $table = $this->argument('table');
        
        if (!$table) {
            $this->error("Table name required atau gunakan --all untuk generate semua tabel");
            return 1;
        }

        return $this->generateSeederForTable($table);
    }

    private function generateSeederForTable($table, $modelName = null, $className = null)
    {
        $modelName = $modelName ?: $this->option('model') ?: Str::studly(Str::singular($table));
        $className = $className ?: $this->option('class') ?: Str::studly(Str::singular($table)) . 'Seeder';

        // Check if table exists
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            $this->error("Table '{$table}' tidak ditemukan!");
            return 1;
        }

        // Get all data from table
        $data = DB::table($table)->get();

        if ($data->isEmpty()) {
            $this->warn("Table '{$table}' kosong. Tidak ada data untuk di-generate.");
            return 0;
        }

        $this->generateSeederForTableInternal($table, $modelName, $className, $data);
        
        $this->info("Seeder berhasil dibuat: database/seeders/{$className}.php");
        $this->info("Total data: " . $data->count() . " records");

        return 0;
    }

    private function generateSeederForTableInternal($table, $modelName, $className, $data = null)
    {
        if ($data === null) {
            $data = DB::table($table)->get();
        }

        // Get table columns
        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        // Generate seeder content
        $seederContent = $this->generateSeederContent($table, $modelName, $className, $data, $columns);

        // Write to file
        $seederPath = database_path("seeders/{$className}.php");
        file_put_contents($seederPath, $seederContent);

        $this->info("  ✓ {$className} - {$data->count()} records");
    }

    private function generateAllSeeders()
    {
        // List of tables to generate seeders for
        $tables = [
            'tabel_cabang' => ['model' => 'Cabang', 'class' => 'CabangSeeder'],
            'bahan_baku' => ['model' => 'BahanBaku', 'class' => 'BahanBakuSeeder'],
            'produk' => ['model' => 'Produk', 'class' => 'ProdukSeeder'],
            'supplier' => ['model' => 'Supplier', 'class' => 'SupplierSeeder'],
            'users' => ['model' => 'User', 'class' => 'UserSeeder'],
        ];

        $this->info("Generating seeders untuk semua tabel...\n");

        foreach ($tables as $table => $config) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $data = DB::table($table)->get();
                    if ($data->isNotEmpty()) {
                        $this->line("Processing table: {$table}...");
                        $this->generateSeederForTableInternal($table, $config['model'], $config['class']);
                        $this->line("");
                    } else {
                        $this->warn("Table '{$table}' kosong, dilewati.");
                    }
                } else {
                    $this->warn("Table '{$table}' tidak ditemukan, dilewati.");
                }
            } catch (\Exception $e) {
                $this->error("Error processing table '{$table}': " . $e->getMessage());
            }
        }

        $this->info("\nSelesai!");
        return 0;
    }


    private function generateSeederContent($table, $modelName, $className, $data, $columns)
    {
        $modelClass = "App\\Models\\{$modelName}";
        $dataArray = $this->formatDataArray($data, $columns);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use {$modelClass};\n\n";
        $content .= "class {$className} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run(): void\n";
        $content .= "    {\n";
        $content .= "        \$data = [\n";

        foreach ($data as $index => $row) {
            $content .= "            [\n";
            foreach ($columns as $col) {
                $value = $row->$col;
                $formattedValue = $this->formatValue($value);
                $content .= "                '{$col}' => {$formattedValue},\n";
            }
            $content .= "            ],\n";
        }

        $content .= "        ];\n\n";
        $content .= "        foreach (\$data as \$item) {\n";
        $content .= "            {$modelName}::updateOrCreate(\n";
        
        // Determine unique keys (usually id or combination of unique columns)
        $uniqueKeys = $this->getUniqueKeys($table, $columns);
        if (in_array('id', $columns)) {
            $content .= "                ['id' => \$item['id']],\n";
        } elseif (!empty($uniqueKeys)) {
            $uniqueConditions = array_map(fn($key) => "'{$key}' => \$item['{$key}']", $uniqueKeys);
            $content .= "                [" . implode(', ', $uniqueConditions) . "],\n";
        } else {
            // Use all columns as unique condition
            $allConditions = array_map(fn($col) => "'{$col}' => \$item['{$col}']", $columns);
            $content .= "                [" . implode(', ', $allConditions) . "],\n";
        }
        
        $content .= "                \$item\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('{$modelName} berhasil di-seed!');\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    private function formatDataArray($data, $columns)
    {
        $result = [];
        foreach ($data as $row) {
            $item = [];
            foreach ($columns as $col) {
                $item[$col] = $row->$col;
            }
            $result[] = $item;
        }
        return $result;
    }

    private function formatValue($value)
    {
        if ($value === null) {
            return 'null';
        } elseif (is_numeric($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return "'" . addslashes($value) . "'";
        }
    }

    private function getUniqueKeys($table, $columns)
    {
        // Try to get unique indexes
        $indexes = DB::select("SHOW INDEXES FROM `{$table}` WHERE Non_unique = 0");
        $uniqueKeys = [];
        
        foreach ($indexes as $index) {
            if ($index->Key_name !== 'PRIMARY' && in_array($index->Column_name, $columns)) {
                $uniqueKeys[] = $index->Column_name;
            }
        }

        return array_unique($uniqueKeys);
    }
}
