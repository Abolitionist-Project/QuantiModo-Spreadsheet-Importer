QuantiModo-Converters
=====================

PHP Modules to Extract from Spreadsheets, Transform to QM Format, and Load to the QM Measurements Table

# Adding a Converter

1. Add `require_once` entry in `/PHPConvert/ConverterManager.php`
2. Add an instance of your converter to the array of converters

### Example: add MyFitnessCompanionConverter:
1. Browse to `/PHPConnect`
2. Open `ConverterManager.php`
3. Add the line `require_once 'Converters/MyFitnessCompanionConverter.php';`
4. Add the line `$this->converters[] = new MyFitnessCompanionConverter();` in the constructor
