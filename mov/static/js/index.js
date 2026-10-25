function pageLoad(){

	var pageData = {};
	// 切换标记
	pageData.mark = {'dianshi': 0,'dianying': 0,'zongyi': 0,'dongman': 0};
	var listTypes = ['dianshi','dianying','zongyi','dongman'];

	// 初始化轮播
	$('.s-slider').mySlider({navAlign:'right'});

	// 精确测量 Grid 当前实际列数：直接读浏览器计算出的 grid-template-columns。
	// 相比 offsetTop 判行法，不受布局/reflow 时序影响，绝对可靠。
	function gridCols($grid){
		if(!$grid.length) return 0;
		var cols = (getComputedStyle($grid[0]).gridTemplateColumns || '').split(' ').filter(function(s){return s && s !== 'none';}).length;
		return cols;
	}

	// 测量某列表列数：临时全显示（不可见方式），用 getComputedStyle 读真实列数。
	// 保持 CSS 的 auto-fit + minmax 自适应网格不变，让窗口越宽自动加列、卡片宽度恒定，
	// 这样最大化时图片不会因锁列被拉伸变大。
	function measureCols($grid, $items){
		var prev = [];
		$items.each(function(){ prev.push(this.style.display); });
		$grid.css('visibility','hidden');
		$items.css('display','block');
		var cols = gridCols($grid);
		$items.each(function(i){ this.style.display = prev[i]; });
		$grid.css('visibility','');
		return cols;
	}

	// 桌面端每类默认显示 2 整行（实际列数 × 2），每行填满、绝不留空格
	function applyShowNum(){
		var isDesktop = window.innerWidth >= 800;
		for(var t = 0;t < listTypes.length;t++){
			var type = listTypes[t];
			var $grid = $('#' + type + 'List');
			var $items = $grid.find('a');
			if(!$items.length) continue;
			pageData.mark[type] = 0;
			if(!isDesktop){
				// 手机端：保持默认 6 个（3 列 × 2 行），不改动网格
				$items.each(function(i){ this.style.display = (i < 6) ? 'block' : 'none'; });
				continue;
			}
			var cols = measureCols($grid, $items);
			if(!cols) continue;
			var total = $items.length;
			var rowsAvail = Math.floor(total / cols);					// 数据能凑出的整行数
			var rows = Math.min(2, rowsAvail < 1 ? 1 : rowsAvail);		// 桌面默认 2 行
			var show = cols * rows;										// 显示数量 = 列数 × 行数
			$items.each(function(i){ this.style.display = (i < show) ? 'block' : 'none'; });
		}
	}

	// 加载时应用一次；窗口改变时实时自适应（防抖，避免频繁触发）
	applyShowNum();
	var resizeTimer = null;
	$(window).on('resize', function(){
		if(resizeTimer) clearTimeout(resizeTimer);
		resizeTimer = setTimeout(applyShowNum, 200);
	});

	// 换一批：每批也是整数行（桌面=实际列数×2，手机=6）
	$('.switch-button').tap(function(){ loadPageData($(this).attr('data-list-type')); })
	function loadPageData(listType){
		var $grid = $('#' + listType + 'List');
		var $items = $grid.find('a');
		var total = $items.length;
		var pageSize;
		if(window.innerWidth < 800){
			pageSize = 6;
		}else{
			var cols = gridCols($grid);
			pageSize = cols ? cols * 2 : total;
		}
		var markMax = Math.max(0, Math.ceil(total / pageSize) - 1);	// 最大切换次数
		++ pageData.mark[listType];
		if(pageData.mark[listType] > markMax){
			pageData.mark[listType] = 0;
		}
		var start = pageData.mark[listType] * pageSize;
		$items.each(function(i){
			this.style.display = (i >= start && i < start + pageSize) ? 'block' : 'none';
		});
	}

	pageLoaded = true;
}
if(typeof(pageLoaded) == 'undefined' && typeof(jsApi) != 'undefined'){ pageLoad(); }