<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\Storage;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from CSV files';

    public function handle()
    {
        $this->importCategory('batteries.csv', 'battery', 'capacity');
        $this->importCategory('solar_panels.csv', 'panel', 'power_output');
        $this->importCategory('connectors.csv', 'connector', 'connector_type');

        $this->info('Import completed successfully.');
    }

    private function importCategory($filename, $category, $attributeKey)
    {
        $path = "data/{$filename}";

        if (!Storage::exists($path)) {
            $this->error("File not found: {$path}");
            return;
        }

        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));

        array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) < 6) {
                continue;
            }

            [$id, $name, $manufacturer, $price, $attrValue, $description] = $row;

            if (!is_numeric($id)) {
                continue;
            }

            try {
                $product = Product::firstOrCreate(
                    ['id' => $id],
                    [
                        'name' => $name,
                        'manufacturer' => $manufacturer,
                        'price' => floatval($price),
                        'category' => $category,
                        'description' => $description,
                    ]
                );

                if ($product->wasRecentlyCreated) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'key'        => $attributeKey,
                        'value'      => $attrValue,
                    ]);
                    $this->info("Inserted product ID: $id");
                } else {
                    $this->info("Product ID $id already exists, skipping...");
                }

            } catch (\Throwable $e) {
                $this->error("Failed inserting product ID: $id — " . $e->getMessage());
                continue;
            }
        }

        $this->info("Imported: {$filename}");
    }
}
