/**
 * 
 */
function random(min, max) {
	var num = Math.random();
	return Math.round((max - min) * num + min);
}

function random_ip() {
	return random(1, 255) + "." + random(0, 255) + "." + random(0, 255) + "." + random(1, 255);
}

function random_mac() {
	var mac = "";
	for(var i = 0;i < 6; i ++) {
		var num = random(0, 255);
		if(num <= 16) {
			mac += "0";
		}
		mac += num.toString(16);
		if(i < 5) {
			mac += ":";
		}
	}
	return mac;
}

document.write(random_ip());
document.write("\n");
document.write(random_mac());
