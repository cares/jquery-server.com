/* UNTESTED! search and replace */
function replaceScript(searchIn,For,replaceWith) {
    return searchIn.replace(For, replaceWith);
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
/* returns the last char of a string */
function returnLastChar(string) {
	var result = string.substring(string.length, string.length - 1);
	return result;
}
/* returns the last X chars of a string */
function returnLastXChars(string,X) {
	var result = string.substring(string.length, string.length - X);
	return result;
}

/* chops last char of a string */
function chopLastChar(string) {
	return string.substring(0, string.length - 1);
}
/* chops last X char of a string */
function chopLastCharX(string, X) {
	return string.substring(0, string.length - X);
}
/* replace char at position */
function replaceAt(input, index, newChar) {
	return input.substr(0, index) + newChar + input.substr(index + newChar.length);
}
/* reverse string */
function reverseString(input) {
	var result = "";
	result = input.split("").reverse().join("");
	return result;
}
/* get first X strings */
function returnFirstXChars(string,X)
{
	var result = string.substring(0, X);
	return result;
}