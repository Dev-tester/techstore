{"version":3,"sources":["projectplan.js"],"names":["BX","namespace","Tasks","Shared","Form","ProjectPlan","Util","Widget","extend","sys","code","options","matchWorkTime","companyWorkTime","methods","construct","this","callConstruct","instances","vars","blockSignal","option","data","optionP","cwt","COMPANY_WORKTIME","TIME_UNIT_TYPE_DAY","TIME_UNIT_TYPE_HOUR","TIME_UNIT_TYPE_MINUTE","WORKDAY_DURATION","getWorkDayDuration","unit","TASK","DURATION_TYPE","d","getDeadlinePicker","sdpp","getStartDatePlanPicker","edpp","getEndDatePlanPicker","duration","start","getTimeStamp","startDate","Date","end","endDate","calculateDuration","setDurationSeconds","bindInstantChange","getDurationControl","passCtx","debounce","onDurationChange","bindDelegateControl","preventEnter","onUnitChange","control","startDatePlanPicker","scope","type","isElementNode","DatePicker","defaultTime","HOURS","START","bindEvent","delegate","onStartDatePlanChange","endDatePlanPicker","END","onEndDatePlanChange","deadlinePicker","onDeadLineChange","stamp","checkNoWorkDays","solveDeadline","fireEvent","getValue","solveTriangle","e","isEnter","PreventDefault","result","calender","getCalendar","weekends","dayNumbers","forEach","dayNumber","setMatchWorkTime","way","startProjectPlanPicker","endProjectPlanPicker","recalculateDuration","parent","calendar","Calendar","adaptSettings","setDeadline","date","disableChangeEvent","setTimeStamp","getTime","enableChangeEvent","fixDeadline","isWorkTime","getClosestWorkTime","setStartDate","fixStartDate","setEndDate","fixEndDate","showHintPopup","picker","hintManager","show","message","autoHide","calculateStartDate","calculateEndDate","deadline","fixedDeadline","startChange","endChange","durationChange","durationMs","defaultDuration","getMultiplier","defaultDurationMs","isMaxDurationReached","fixedStartDate","fixedEndDate","node","getDuration","value","isNotEmptyString","setDurationUnit","isNaN","getMaxDuration","switchControlMode","parentElement","isErrorMode","addClass","removeClass","recalc","dropCSSFlags","setCSSFlag","getUnitByDuration","units","i","length","durationInUnit","Math","floor","parseInt"],"mappings":"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","file":"projectplan.map.js"}