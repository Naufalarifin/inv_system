
$(document).ready(function(){
	
	
	/* ---------- Chart with points ---------- */
	var id_chart="#sincos";

	if($(id_chart).length)
	{
		var NIP_0201 = [], NIP_0202 = [], NIP_0203 = [], NIP_0204 = [];

		for (var i = 1; i < 13; i += 1) {
			NIP_0201.push([i, 500]);
			NIP_0202.push([i, 700]);
			NIP_0203.push([i, 1300]);
			NIP_0204.push([i, 1100]);
		}

		var plot = $.plot($(id_chart),
			   [ 
			   	{ data: NIP_0201, label: "0201"}, 
			   	{ data: NIP_0202, label: "0202" }, 
			   	{ data: NIP_0203, label: "0203" },
			   	{ data: NIP_0204, label: "0204" },

			   	], {
				   series: {
					   lines: { show: true,
								lineWidth: 2,
							 },
					   points: { show: true },
					   shadowSize: 2
				   },
				   grid: { hoverable: true, 
						   clickable: true, 
						   tickColor: "#dddddd",
						   borderWidth: 0 
						 },
				   yaxis: { min: 0, max: 2000 },
				   colors: ["#FA5833", "#2FABE9"]
				 });

		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#dfeffc',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}

		var previousPoint = null;
		$(id_chart).bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;

						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(0),
							y = item.datapoint[1].toFixed(0);

						showTooltip(item.pageX, item.pageY,
									item.series.label + " Bulan " + x + " = " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;
				}
		});
		


		$(id_chart).bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text(item.series.label + " Bulan " + (item.dataIndex+1) + " = " + item.datapoint[1].toFixed(0) );
				plot.highlight(item.series, item.datapoint);
			}
		});
	}
	
});


$(document).ready(function(){
	
	
	/* ---------- Chart with points ---------- */
	var id_chart="#sincos2";

	if($(id_chart).length)
	{
		var NIP_0201 = [], NIP_0202 = [], NIP_0203 = [], NIP_0204 = [];

		for (var i = 1; i < 13; i += 1) {
			NIP_0201.push([i, 500]);
			NIP_0202.push([i, 700]);
			NIP_0203.push([i, 1300]);
			NIP_0204.push([i, 1100]);
		}

		var plot = $.plot($(id_chart),
			   [ 
			   	{ data: NIP_0201, label: "0201"}, 
			   	{ data: NIP_0202, label: "0202" }, 
			   	{ data: NIP_0203, label: "0203" },
			   	{ data: NIP_0204, label: "0204" },

			   	], {
				   series: {
					   lines: { show: true,
								lineWidth: 2,
							 },
					   points: { show: true },
					   shadowSize: 2
				   },
				   grid: { hoverable: true, 
						   clickable: true, 
						   tickColor: "#dddddd",
						   borderWidth: 0 
						 },
				   yaxis: { min: 0, max: 2000 },
				   colors: ["#FA5833", "#2FABE9"]
				 });

		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#dfeffc',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}

		var previousPoint = null;
		$(id_chart).bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;

						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(0),
							y = item.datapoint[1].toFixed(0);

						showTooltip(item.pageX, item.pageY,
									item.series.label + " Bulan " + x + " = " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;
				}
		});
		


		$(id_chart).bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text(item.series.label + " Bulan " + (item.dataIndex+1) + " = " + item.datapoint[1].toFixed(0) );
				plot.highlight(item.series, item.datapoint);
			}
		});
	}
	
});