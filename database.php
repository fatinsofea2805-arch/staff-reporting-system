<?php
/** Small helpers for native PDO SQLite statements and buffered SELECT results. */
final class DatabaseRows
{
    private array $rows;
    private int $position = 0;
    public int $num_rows;

    public function __construct(PDOStatement $statement)
    {
        $this->rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($this->rows);
        $statement->closeCursor();
    }
    public function fetch_assoc(): ?array
    {
        return $this->rows[$this->position++] ?? null;
    }
    public function free_result(): void
    {
        $this->rows = [];
        $this->num_rows = 0;
        $this->position = 0;
    }
}

function db_bind(PDOStatement $statement, string $types, ...$values): bool
{
    if (strlen($types) !== count($values)) {
        throw new InvalidArgumentException('Parameter type and value counts differ.');
    }
    foreach ($values as $index => $value) {
        $type = $value === null ? PDO::PARAM_NULL : ($types[$index] === 'i' ? PDO::PARAM_INT : PDO::PARAM_STR);
        if (!$statement->bindValue($index + 1, $value, $type)) {
            return false;
        }
    }
    return true;
}

function db_query(PDO $connection, string $sql)
{
    $statement = $connection->query($sql);
    if ($statement === false) {
        return false;
    }
    return $statement->columnCount() > 0 ? new DatabaseRows($statement) : true;
}

// Only for existing literal SQL search filters. Bound values must stay unescaped.
function db_escape(PDO $connection, $value): string
{
    return substr($connection->quote((string)$value), 1, -1);
}

function db_error($source): string
{
    return (string)($source->errorInfo()[2] ?? 'Unknown database error');
}
