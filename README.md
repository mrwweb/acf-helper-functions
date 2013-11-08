# Advanced Custom Fields Output Helper Functions

A powerful set of functions for outputting common markup patterns with Advanced Custom Fields Plugin for WordPress.

Formerly a gist at https://gist.github.com/mrwweb/5768363

See Also: ACF Forums discussion at http://support.advancedcustomfields.com/discussion/comment/19680#Comment_19680

Clean, standardized output for common Advanced Custom Fields scenarios.

Two simple functions: `get_acf_field()` and `the_acf_field()`, the latter of which is just a simple wrapper function.

The functions let you replace this:

```php
<?php
$my_field = get_field('my_field');
if( $my_field ) {
  echo '<span class="label">My Field:</span> <span itemprop="name">' . $my_field . '</span>';
}
?>
```

With this:

```php
<?php the_acf_field( 'my_field', array( 'label' => 'My Field', 'itemprop' => 'name' ); ?>
```

## WHY?

It gets annoying to repeat so much code to output a simple ACF field. You have to test if the field is there, add a label, add schema.org markup, etc.

This tries to help take care of that though I don't expect it to handle things as complicated as a Repeater field.

## ARGUMENTS

The `$args` parameter accepts an `array` or query_string-style format.
 
Array Format:
 
`array( 'type' => 'text', 'label' => 'a label' );`
 
Query String Format:
 
`type=text&label=a label`
 
### Argument Documentation

`type` Valid types:

 * `text` **(default)**
 * `image` (field store ID)
 * `email` (outputs as `mailto:` link, uses `link_label` arg if provided)
 * `url` (won't display field if equal to 'http://')
 * `link` (same as URL, but output as link and with optional `link_label` arg)
 * `date` (must be stored as stored as recommended yymmdd format)
 * `post_list` (a delimited list of posts selected with a relationship field)
 * `term` (a single term from a taxonomy, assumes ID is stored)
 * `term_link` (link to a term archive from a taxonomy, assumed ID is stored)
 * `custom` (use the `return_get_acf_field` filter to add more types)

`label` Puts label before field output. Label wrapped in a `<span>`

`link_label` Anchor text for `link` or `email` field types.

`image_size` only used with `'type' => 'image'`

`image_class` only used with `'type' => 'image'`

`itemprop` Wraps field value in `<span>` with the specified schema.org property

`date_format` PHP date format to return. *only used with `'type' => 'date'`*

`before` HTML before the field out

`after` HTML after the field output

`sub_field` Set to `true` if using `get_acf_field` in a repeater or flexible field

`list_sep` Delimiter string for `post_list` type

`list_links` Should the `post_list` items link to posts? Default: `true`

`list_type` Format that relationship field uses to store data. Default: 'objects'

`taxonomy` Taxonomy containing the term. Requried for `term` field.

## TO-DOS

* Add more types (suggestions welcome)
* Add sanitization
* Complete support for `itemprop`

## CHANGELOG

### 13 Jun 2013

 * Added changelog
 * [new] filter to add new types: `return_get_acf_field`
    * (thanks @wells5609: https://gist.github.com/wells5609/5786376)
 * [new] filter for label class: `get_acf_field_label_class`

### 28 Jun 2013
 * [new] post_list type
 * [new] date type
 * [new] `sub_field` argument for use in `while( has_sub_field() ) ...`
 
### 11 Jul 2013
 * [new] term and term_link types added for taxonomy fields for a single term

### 9 Nov 2013
 * [improvement] More consistent, cleaner handling of `itemprop` arg. (Shifting to use of `sprintf`.)
 * [new] `link_label` argument for `email` and `link` field types.
