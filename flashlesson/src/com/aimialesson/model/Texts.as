package com.aimialesson.model
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;
	

	public class Texts extends EventDispatcher
	{
		
		public static const ONLINE:String = "online";
		public static const ONLINE_STUDENT:String = "onlineStudent";
		public static const ONLINE_TEACHER:String = "onlineTeacher";
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
		public static const SLIDES_ARE_GENERATING:String = "slidesAreGenerating";
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
		
		public var text_xml_url:String = "/flash/lessontexts.xml";
		
		public function setTexts():void { // just an attempt to fix the emty strings issue - on first loading. texts shoul
			var newTexts:Array;
			switch (lang){
				case (Texts.EN)	:	texts = enTexts;
									return;
									break;
				case (Texts.JA)	:	newTexts = jaTexts;
									break;
				case (Texts.ZH)	:	newTexts = zhTexts;
									break;
				case (Texts.RU)	:	newTexts = ruTexts;
									break;
			}
			texts = new Array();
			for (var i:String in enTexts){
				if (newTexts[i])
					texts[i] = newTexts[i];
				else texts[i] = enTexts[i];
			}
		//	if (!texts[Texts.NO] && !texts[Texts.ALERT]) texts = enTexts;
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
			text = enTexts[value];
			switch (lang){
				//case (Texts.EN)	:	text = enTexts[value];
					//				break;
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