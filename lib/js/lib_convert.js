/* JavaScript variable types
// Numbers
typeof 37 === 'number';
typeof 3.14 === 'number';
typeof Math.LN2 === 'number';
typeof Infinity === 'number';
typeof NaN === 'number'; // Despite being "Not-A-Number"
typeof Number(1) === 'number'; // but never use this form!

// Strings
typeof "" === 'string';
typeof "bla" === 'string';
typeof (typeof 1) === 'string'; // typeof always return a string
typeof String("abc") === 'string'; // but never use this form!

// Booleans
typeof true === 'boolean';
typeof false === 'boolean';
typeof Boolean(true) === 'boolean'; // but never use this form!

// Undefined
typeof undefined === 'undefined';
typeof blabla === 'undefined'; // an undefined variable

// Objects
typeof {a:1} === 'object';
typeof [1, 2, 4] === 'object'; // use Array.isArray or Object.prototype.toString.call to differentiate regular objects from arrays
typeof new Date() === 'object';

typeof new Boolean(true) === 'object'; // this is confusing. Don't use!
typeof new Number(1) === 'object'; // this is confusing. Don't use!
typeof new String("abc") === 'object';  // this is confusing. Don't use!

// Functions
typeof function(){} === 'function';
typeof Math.sin === 'function';

null

typeof null === 'object'; // This stands since the beginning of JavaScript
*/

// runTests(); // uncomment this, include convert.js <script type="text/javascript" src="js/convert.js"></script> and fire up your browser (with firebug) to run these tests.

/* run tests */
function runTests()
{
	var number = string2int("123");
	console.log("string2int:"+(typeof number === 'number')+" the type is: "+(typeof number));
	
	var number = string2float("0.123");
	console.log("string2float:"+(typeof number === 'number')+" the type is: "+(typeof number));

	var string = float2string(0.123);
	console.log("string2float:"+(typeof string === 'string')+" the type is: "+(typeof string));
	
	var string = int2string(123);
	console.log("string2int:"+(typeof string === 'int')+" the type is: "+(typeof string));
}
function dec2octal(input) {
	input = parseInt(input);
	var octal = input.toString(8);
	return octal;
}
function dec2bin(input) {
	input = parseInt(input);
	var bin = input.toString(2);
	return bin;
}
/* convert a binary to decimal */
function bin2dec(input) {
	var result = "";
	// keep binary as string
	result = parseInt(input, 2); // radix 2
	return result;
}
/* convert a decimal to hex*/
function dec2hex(input) {
	var result = "";
	result = parseInt(input);
	// result = result + 255; // der schlaubi schlumpf meint das muss so http://st-on-it.blogspot.de/2009/07/decimal-to-hex-and-hex-to-decimal-in.html wehe er hat nicht recht.
	result = result.toString(16).toUpperCase();

	return result;
}
/* convert a hex to decimal */
function hex2dec(input) {
	var result = "";
	// keep binary as string
	result = parseInt(input, 16); // radix 16
	return result;
}
/* fill up with zeros */
function fillUp(input, digits) {
	var result = "";
	input = input.toString();
	while (input.length < digits) {
		input = "0" + input;
	}
	return input;
}
/* convert bin2hex */
function bin2hex(input) {
	var result = "";
	var dec = bin2dec(input);
	result = dec2hex(dec);

	return result;
}
/* convert hex2bin */
function hex2bin(input) {
	var result = "";
	var dec = hex2dec(input);
	result = dec2bin(dec);

	return result;
}
/*
parseFloat('1.45kg')  // 1.45
parseFloat('77.3')    // 77.3
parseFloat('077.3')   // 77.3
parseFloat('0x77.3')  // 0
parseFloat('.3')      // 0.3
parseFloat('0.1e6')   // 100000
parseInt syntax:   parseInt( string [, base] )
 */ 
/* convert string2int */
function string2int(input) {
	var result = null;
	result = parseInt(input);
	return result;
}
/* convert string2float */
function string2float(input) {
	var result = null;
	result = parseFloat(input);
	return result;
}

/*
 * http://www.javascripter.net/faq/converti.htm
 * Answer: The simplest way to convert any variable to a string is to add an empty string to that variable (i.e. concatenate it with an empty string ''), for example:

a = a+''     // This converts a to string
b += ''      // This converts b to string

5.41 + ''    // Result: the string '5.41'
Math.PI + '' // Result: the string '3.141592653589793'

Another way to perform this conversion is the toString() method:

a = a.toString()     // This converts a to string
b = b.toString()     // This converts b to string
*/
/* convert int2string */
function int2string(input) {
	var result = null;
	result = input.toString();
	return result;
}

/* convert float2string */
function float2string(input) {
	var result = null;
	result = input.toString();
	return result;
}