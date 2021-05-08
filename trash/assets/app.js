var api = "https://final-for-ase220.herokuapp.com/API/";

function findGetParameter(parameterName){
	var result = null,
	tmp = [];
	location.search
	.substr(1)
	.split("&")
	.forEach(function (item) {
	tmp = item.split("=");
	if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
	});
	return result;
}