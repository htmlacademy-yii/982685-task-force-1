<?php
namespace taskforce\utils;

use taskforce\exceptions\{FileImportException, FileFormatException};
use SplFileObject;

abstract class AbstractCsvConverter
{
    protected string $filename;
    protected array $columns;
    protected array $srcData = [];
    protected SplFileObject $srcFileObject;
    protected SplFileObject $destFileObject;

    /**
     * КОНСТРУКТОР класса AbstractCsvConverter
     * @param string $filename              исходный CSV-файл 
     * @param array $columns                названия заголовков таблицы
     */
    public function __construct(string $filename, array $columns)
    {
        $this->filename = $filename;
        $this->columns = $columns;
    }

    /**
     * Считывает данные из CSV-файла и преобразует их в SQL запросы для добавления в MySQL
     * @throws FileFormatException          исключение в случае неправильного формата исходного файла
     * @throws FileImportException          исключение в случае ошибок работы с файлами
     */
    public function сonverter(): void
    {
        if (!$this->validateColumns($this->columns)) {
            throw new FileFormatException("Заданы неверные заголовки столбцов");
        }

        if (!file_exists($this->filename)) {
            throw new FileImportException("Файл не существует");
        }

        try {
            $this->srcFileObject = new SplFileObject($this->filename);
        } catch (FileImportException $e) {
            throw new FileImportException("Не удалось открыть файл на чтение: " . $e->getMessage());
        }
        
        $this->srcFileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE);
        
        $headerData = $this->getHeaderData();

        if ($headerData !== $this->columns) {
            throw new FileFormatException("Исходный файл не содержит необходимых столбцов");
        }
        
        foreach ($this->getNextLine() as $line) {
            $this->srcData[] = $line;
        }
        
        $sql = $this->csv2sql();

        // формируем имя выходного файла из имени исходного файла
        $pathParts = pathinfo($this->filename);
        
        // проверяем, существует ли подкаталог 'sql'
        // если нет, то создаем
        $subdir = $pathParts['dirname'] . "\\sql";
        if (!is_dir($subdir)) {
            mkdir($subdir);
        }
        
        $destFilename = $pathParts['dirname'] . "\\sql\\" . $pathParts['filename'] . ".sql";
        
        if (file_exists($destFilename)) {
            if (!is_writable($destFilename)) {
                throw new FileImportException("Невозможно перезаписать файл");
            }
        }

        try {
            $this->destFileObject = new SplFileObject($destFilename, 'w');
            $this->destFileObject->fwrite($sql);
        } catch (FileImportException $e) {
            throw new FileImportException("Не удалось записать файл: " . $e->getMessage());
        }
    }

    /**
     * Возвращает данные, экспортированные из CSV-файла
     * @return array                        массив с данными
     */
    protected function getData(): ?array
    {
        // фильтруем массив данных от пустых значений
        return array_filter($this->srcData, function ($item) {
            return (count($item) > 1) ? !empty($item) : null; 
        });
    }

    /**
     * Считывает первую строку CSV-файла (заголовок) 
     * @return array|bool                   массив столбцов из заголовка файла
     */
    protected function getHeaderData(): ?array
    {
        $this->srcFileObject->rewind();

        return $this->srcFileObject->fgetcsv();
    }

    /**
     * Считывает следующую строку CSV-файла
     * @return iterable|bool                массив с данными
     */
    protected function getNextLine(): ?iterable
    {
        while (!$this->srcFileObject->eof()) {
            yield $this->srcFileObject->fgetcsv();
        }

        return false;
    }

     /**
     * Проверяет, содержит ли CSV-файл заголовок с требуемыми столбцами 
     * @param array $columns                массив с проверяемыми столбцами
     * @return bool                         true, если заголовок правильный
     */
    protected function validateColumns(array $columns): bool
    {
        $result = true;

        if (count($columns)) {
            foreach ($columns as $column) {
                if (!is_string($column)) {
                    $result = false;
                }
            }
        }
        else {
            $result = false;
        }

        return $result;
    }

    /**
     * Преобразует данные из массива $srcData в SQL запрос
     * @return string                         SQL запрос
     */
    abstract protected function csv2sql(): string;
}
