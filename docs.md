# Using the Code Builder

Bonfire ships with a flexible code builder that is intended to help you create boilerplate code for your modules to streamline your development process and help ensure you don't forget those crucial little details when putting your own modules together. It ships with all you need to create your own:

- Models
- Controllers
- Migrations
- Forms
- Contexts
- Even entire modules

It's designed to be flexible and powerful enough to allow you to easily create your generators with a minimum of time. You can also customize the templates for any of the generators that you have installed in your application.

## Generating Code


## Creating New Generators

You can create your own generators for you personal use, or for sharing with other devs, quickly and easily.

### Generator Location

Each generator must have it's own folder under the the <tt>core_modules/builder/generators</tt> folder. The folder name must match the name of the generator in general.

Let's say you're creating a generator to build a form. That folder must be named <tt>form</tt> and be located at <tt>core_modules/builder/generators/form</tt> in order for the Code Builder to find it.

### Folder Structure

Within the generator folder, you will have the following file and folder structure:

```html+php
templates/
{generator_name}_generator.php
```

The <tt>templates</tt> folder stores the files that form the templates of any files or views that your module will generate.

The only required file is the generator file which is name exactly the same as the generator folder, with <tt>_generator</tt> appended to the name. For our form generator, the file would be named <tt>form_generator.php</tt>.

### The Generator File

The generator file is a simple PHP class that extends the <tt>BF_Generator</tt> class to add the specific functionality that your generator needs. The *BF_Generator* provides lots of tools and default capabilities for you so that making a generator is as easy as possible.

A skeleton genetor file looks like this:

```html+php
class {Name}_Generator extends BF_Generator {

	public $title 		= '';
	public $description	= '';
	public $template 	= '';

	protected $fields = array();

	//--------------------------------------------------------------------

	public function generate($params)
	{
		. . .
	}

	//--------------------------------------------------------------------

}
```

The first thing is to change the <tt>{Name}</tt> placeholder to match the name of your generator. This must be Initial caps and words separated by underscores.

**$title** The <tt>$title</tt> variable holds the name of your generator as you want it displayed in menus, on the generator page, etc. This will typically be the name of your generator plus the word 'Generator'. Like <tt>Form Generator</tt>.

**$description** The <tt>$description</tt> variable holds the description of your generator. This will also appear on the main Code Builder page as well as the first line on your Generator's form page.

**$template** The <tt>$template</tt> stores the name or names of the template files used by your application.

**$fields** This stores the information that your generator needs to collect from the user at generation time. Full details are discussed below.

The <tt>generate()</tt> method is what is called when it comes time to actually perform the generation. The <tt>$params<tt> parameter is an array of all information collected from the form in an associative array. At this point, the fields have already been validated so you can simply proceed with your code generation.


## The Fields Array