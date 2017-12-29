# werx-validation--formvalidator-net-interface

### A php interface function (yes, a blank function, not an abstract class) which transforms  
## werx/validation ( https://github.com/werx/validation ) rules  
### into  
## jQuery Form Validator ( http://www.formvalidator.net )  tags.

# Examples:
#### Ruleset: 
### startswith[abc]|required|minlength[4]|maxlength[8]
#### Resulting html control tags: 
### data-validation="custom required length" data-validation-regexp="^abc" data-validation-length="4-8"

#### Ruleset: 
### contains[abc]|required|minlength[4]|maxlength[13]
#### Resulting html control tags: 
### data-validation="custom required length" data-validation-regexp="abc" data-validation-length="4-13"

#### Ruleset:
### required|maxlength[13]|integer
#### Resulting html control tags: 
### data-validation="required length number" data-validation-length="0-13" data-validation-allowing="range[-2147483647,2147483647,negative]"

#### Ruleset:
### required|greaterthan[-314]|numeric
#### Resulting html control tags: 
### data-validation="required number" data-validation-allowing="range[-314,2147483647,negative]"

### The resulting string can be simply added to any control... for example:
```php
$result = getFormvalidatorString('startswith[abc]|required|minlength[4]|maxlength[8]');
$control='<input type="text" id="mytextid" name="mytext" ' . $result . ' />';
```
which will generate the following:
```html 
<input type="text" id="mytextid" name="mytext" data-validation="custom required length" data-validation-regexp="^abc" data-validation-length="4-8" />
```
So after including the js file <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
you can check whether the input value is valid or not
```javascript
$('#mytextid' ).validate(function(valid, elem) {
          if (valid) {
              console.log('it is valid');
         } else {
              console.log('It is Invalid');
         }
} );
```
