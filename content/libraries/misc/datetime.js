function getTimestamp(){
	var today = new Date();
	var ampm = "am";
	if(today.getHours() >= 12){
		ampm = "pm";
	}
	var minutes = today.getMinutes();
	if(minutes <= 9){
		minutes = "0"+minutes;
	}
	var hours = today.getHours();
	if(hours > 12){
		hours = hours-12;
	}

	var time = hours+":"+minutes+ampm;

	var stringDate = today.getMonth()+"/"+today.getDate()+"/"+today.getFullYear()+" @"+time;
	return stringDate.toString();
}