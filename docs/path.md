It is a utility designed to simplify path related operations, such as joining or normalizing path parts.

## join

Allows to join all given path parts together using system
specific directory separator. It ignores empty arguments and
excessive separators.

**Example:**
```php
echo Path::join('/disk', 'my/dir', 'file.txt');
```
Result:
```
/disk/my/dir/file.txt
``` 
## joinWithTemp

Similar to Path::join, but additionaly it adds system specific
temporary directory path at the beginning.

**Example:**
```php
echo Path::joinWithTemp('/disk', 'my/dir', 'file.txt');
```
Result:
```
/tmp/disk/my/dir/file.txt
```
## normalize

It normalizes given path by removing unncessary references
to parent directories (i.e. "..") and removing double slashes.

**Example:**
```php
echo Path::normalize('/disk/..//photo.jpg');
```
Result:
```
/photo.jpg
```
