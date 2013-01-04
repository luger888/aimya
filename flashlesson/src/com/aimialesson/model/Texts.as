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
		public static const LESSON_FOCUS:String = "lessonFocus";
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
		public static const UPLOAD_MORE:String = "uploadMore";
		public static const THE_LESSON_IS_FINISHED:String = "lessonIsFinished";
		public static const ALERT:String = "alert";
		public static const ARE_YOU_SURE_YOU_WANT_TO_END_THE_LESSON:String = "wantEndLesson";
		public static const YES:String = "yes";
		public static const OK:String = "ok";
		public static const NO:String = "no";
		
		public static const EN:String = "en";
		public static const RU:String = "ru";
		public static const JA:String = "ja";
		public static const ZH:String = "zh";
		
		public var enTexts:Array;
		public var jaTexts:Array;
		
		public var zhTexts:Array;
		public var ruTexts:Array;
		
		[Bindable]
		public var texts:Array;
		
		public function setTexts():void { // just an attempt to fix the emty strings issue - on first loading. texts shoul
			switch (lang){
				case (Texts.EN)	:	texts = enTexts;
					break;
				case (Texts.JA)	:	texts = jaTexts;
					break;
				case (Texts.ZH)	:	texts = zhTexts;
					break;
				case (Texts.RU)	:	texts = ruTexts;
					break;
			}
		}
		
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
			debug ("getText:" + value);
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
			debug (text);
			if (upperCase)
				return text.toLocaleUpperCase();
			else return text;
		}
		
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}