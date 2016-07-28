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

# Adding a Reader

1. Add `require_once` entry in `/PHPConvert/ReaderManager.php`
2. Add an instance of your reader to the array of reader, make sure the CSVReader is last

### Example: add SQLiteReader:
1. Browse to `/PHPConnect`
2. Open `ReaderManager.php`
3. Add the line `require_once 'Readers/SQLiteReader.php';`
4. Add the line `$this->converters[] = new SQLiteReader();` in the constructor, before CSVReader

# Example Spreadsheet: Medhelper
**Here's HTML export example:**
* https://drive.google.com/file/d/0B5728AmQzoPQUkdSWW9LSU5EcWo4VFREY0g3dFVzQU5XSXRv/edit?usp=sharing

**Here's a spreadsheet export example:**
* https://drive.google.com/file/d/0B5bXsrxQCOy8WDBxYW4ycVpJdkE/edit?usp=sharing

Any non-existent variables should be created in the medications category.
