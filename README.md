# Dynamic Dummy Image Generator<br>— as seen on DummyImage.com
**This script enables you to create placeholder images in a breeze.**

## Usage
Just link to the `index.php` file via the HTML `<img>` element. For example, would create a PNG image with red (#f00) background, white (#ffffff) text, the word “Dummy” written on it and a size of 500px width, 250px height.

```html
Classic usage:
<img src="folder-image/?size=500x250&type=png&bg=f00&color=ffffff&text=Dummy" alt="Dummy Image">

Image with specific default value: 
<img src="folder-image/?cfg=folder_thumb" alt="Dummy Image">
```

This script handles the following parameters, where basically all of them are optional.

* `size` *(default: 640x480) — Examples:<br>500x250 (= 500px width, 250px height)<br>500 (= 500px square) *
* `type` *(default: png)* — Examples:<br>png (= PNG image)<br>gif (= GIF image)<br>jpeg or jpg (= JPEG image)
* `bg` *(default: 0099ff)* — Examples:<br>f00 (= #FF0000 as background color)<br>FF0855 (= #FF0855 as background color)
* `color` *(default: FFFFFF)* — Examples:<br>000 (= #000000 as font color)<br>FFFFFF (= #FFFFFF as font color)
* `text` *(default: {WidthOfTheImage}×{HightOfTheImage})* — Examples:<br>Lore Ipsum (= Image has Lore Ipsum written on it)

All default value is set on [config.ini](./config.ini), you can edit this file for your project, and add several parameters for different folders (you must create new section on ini file).


## Htaccess Url Rules (apache)
### Auto-Image with custom name / size
If you use apache, a [.htaccess](./.htaccess) is available with these rules:

```text
IMG name files:
[text].[type]
[width]x[height].[type]
[text]-[width]x[height].[type]
```

Examples:

```html
Classic usage on root folder:
<img src="folder-image/Hello_World.jpg" alt="Hello World"><br>
<img src="folder-image/300x800.jpg" alt="Custom size: 300x800"><br>
<img src="folder-image/Hello_World-200x150.jpg" alt="Hello World with custom size: 200x150">
```


### Virtual support folder and sub folder with specific rules (optional)
You can use the [.htaccess](./.htaccess) rules for support **virtual folder and sub folder** (IMG use precedents name convention):

```text
[folder-cfg OR folder name]/[img]
[folder name]/[folder-cfg OR folder name]/[img]
[folder name]/[folder name]/[folder-cfg OR folder name]/[img]
[folder name]/[folder name]/[folder name]/[folder-cfg OR folder name]/[img]
```

```html
Folder and Sub folder support (ini cfg support on last folder name):
<img src="folder-image/thumb/Lorem_Ipsu.jpg" alt="Lorem_Ipsu in thumb folder">
<img src="folder-image/any/virtual/folder/thumb/150x100.jpg" alt="150x100 in any/virtual/folder/thumb folder">
```

You can add specific parameters _(size, bgcolor, etc)_ for a folder with a CFG configuration in [config.ini](./config.ini) file. Example (cfg rule "thumb"):

```ini
; Setting by default
[default]
;...

; Setting for folder with name "thumb"
[thumb]
size=150x100
bg_color=3322ff
text_color=ffffff
text_value=[WIDTH]×[HEIGHT]
type=jpg
font=RobotoMono-Regular.ttf
```

## Url Rules and virtual folder with Local php-server
If you use local php-server, you can create a file [router.php](./example_phpserver_router.php) on your project on public/web folder _(this file use your index.php)_, and edit this file for use it.

```php
// Edit this const
const ROUTER_EXPREG = '#^(/images/)(film)?/?#i';
const FULLPATH_DUMMY_IMG_GENERATOR = '/path/to/PHP-Dummy-Image-Generator/index.php';

if (preg_match(ROUTER_EXPREG, $_SERVER['REQUEST_URI'], $extract)) {
    $_GET['cfg'] = $extract[2];
    // var_dump($extract);return true; // debug
    require_once FULLPATH_DUMMY_IMG_GENERATOR;
    return true;

} //...
```

You can add several condition for each url rules, add CFG folder on [config.ini](./config.ini) and edit the router template (for more details, look at _Virtual support folder and sub folder with specific rules_).  
For use this router.php, you must launch for start the php-server:

```shell
cd root_project/public
# Or web folder, depending on your project
php -S localhost:8000 router.php
```


## License & Credits
Please see the [license file](./LICENSE) for more information.

Original idea by [Russel Heimlich](https://github.com/kingkool68/). When I first published this script, [DummyImage.com](https://dummyimage.com) was not Open Source, so I had to write a small script to replace the function on my own server.
