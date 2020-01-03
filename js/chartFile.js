	var data = [];
	am4core.ready(function() {
		$('.nuage').each(function(){
			var i = $(this).attr('id').substr(5,5);
			$(this).click(function() {
				var chart;
				if($(this).text() === 'nuage-'){
					$(this).text('nuage+');
					$('#'+i).css( "width", "0");
					$('#'+i).css( "height", "0");
					$('#' + i).empty();
					if(this.chart)
						chart.dispose();
				}else{
					$(this).text('nuage-');
					$('#'+i).css( "width", "100%");
					$('#'+i).css( "height", "300px");
					am4core.useTheme(am4themes_animated);
					chart = am4core.create(i, am4plugins_wordCloud.WordCloud);
					var series = chart.series.push(new am4plugins_wordCloud.WordCloudSeries());
					series.accuracy = 4;
					series.step = 15;
					series.rotationThreshold = 0.7;
					series.maxCount = 200;
					series.minWordLength = 2;
					series.labels.template.tooltipText = "{word}: {value}";
					series.fontFamily = "Courier New";
					series.maxFontSize = am4core.percent(30);
					series.text = data[i];
				}	
			});
		});
		
	});	

