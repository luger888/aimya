package com.aimialesson.model
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;
	

	public class Texts extends EventDispatcher
	{
		
		public static const ONLINE:String = "online";
		public static const STUDENT:String = "student";
		public static const TEACHER:String = "teacher";
		public static const REMAINING_TIME:String = "remainingTime";
		public static const LESSON_PRESENTATION:String = "lessonPresentation";
		public static const MIN:String = "min";
		public static const VIDEO_CHAT:String = "vChat";
		public static const ENTER:String = "enter";
		public static const STOP_SESSION:String = "stopSession";
		public static const MAXIMIZE:String = "maximize";
		public static const MINIMIZE:String = "minimize";
		public static const TOTAL_LESSON_TIME:String = "totalLessonTime";
		public static const LESSON_NOTES:String = "lessonNotes";
		public static const UPLOAD:String = "upload";
		public static const UPLOAD_MORE:String = "upload more";
		public static const THE_LESSON_IS_FINISHED:String = "lessonIsFinished";
		public static const ALERT:String = "Alert:";
		public static const ARE_YOU_SURE_YOU_WANT_TO_END_THE_LESSON:String = "wantEndLesson";
		public static const YES:String = "yes";
		public static const NO:String = "no";
		
		public static const EN:String = "en";
		public static const RU:String = "ru";
		public static const JA:String = "ja";
		public static const ZH:String = "zh";
		
		public var enTexts:Array;
		public var jaTexts:Array;
		public var zhTexts:Array;
		public var ruTexts:Array;
		
		private static var instance : Texts;
		
		public var lang:String = Texts.EN;
		
		public function Texts () {
		}
		
		public static function getInstance () : Texts {
			if (instance == null){
				instance = new Texts();
			}
			return instance;
		}
		
		public function getText ( value : String, upperCase : Boolean = false) : String {
			var text:String;
			switch (lang){
				case (Texts.EN)	:	text = enTexts[value];
									break;
				case (Texts.JA)	:	text = jaTexts[value];
									break;
				case (Texts.ZH)	:	text = zhTexts[value];
									break;
				case (Texts.RU)	:	text = ruTexts[value];
									break;
			}
			if (upperCase)
				return text.toLocaleUpperCase();
			else return text;
		}
	}
}