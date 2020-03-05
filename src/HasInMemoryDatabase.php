<?php

namespace ReedJones\ApiBuilder;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Str;

/**
 * Copied, slimmed, modified, and abused from the Caleb Porzio 'Sushi' package
 * https://github.com/calebporzio/sushi/.
 */
trait HasInMemoryDatabase
{
    protected static $tableName;

    protected static $remoteConnection;

    public function getTable()
    {
        return static::$tableName;
    }

    public function setTable($table)
    {
        static::$tableName = $table;

        return static::$tableName;
    }

    public static function resolveConnection($connection = null)
    {
        return static::$remoteConnection;
    }

    public static function bootHasInMemoryDatabase()
    {
        static::$remoteConnection = app(ConnectionFactory::class)->make([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    public function migrate($rows)
    {
        $this->setTable(Str::random(12));
        $firstRow = $rows[0];
        $serializable = [];

        static::resolveConnection()->getSchemaBuilder()->create($this->getTable(), function ($table) use ($firstRow, &$serializable) {
            foreach ($firstRow as $column => $value) {
                switch (true) {
                    case is_int($value):
                        $type = 'integer';
                        break;
                    case is_numeric($value):
                        $type = 'float';
                        break;
                    case is_string($value):
                        $type = 'string';
                        break;
                    case is_object($value) && $value instanceof \DateTime:
                        $type = 'datetime';
                        break;
                    case is_array($value):
                        array_push($serializable, $column);
                        $type = 'string';
                        break;
                    default:
                        $type = 'string';
                }

                if ($column === $this->primaryKey && $type == 'integer') {
                    $table->increments($this->primaryKey);
                    continue;
                }

                $table->{$type}($column)->nullable();
            }
        });

        if (empty($serializable)) {
            static::insert($rows);
        } else {
            $minimal = collect($rows)
                ->map(function ($data) use ($serializable) {
                    foreach ($serializable as $column) {
                        $data[$column] = json_encode($data[$column] ?? []);
                    }

                    return $data;
                })
                ->toArray();

            static::insert($minimal);
        }

        return $this;
    }

    // No timestamps for you
    public function usesTimestamps()
    {
        return false;
    }
}
