package com.aimialesson.model
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import spark.components.TextArea;

	public class Main extends EventDispatcher
	{
		public var debugger : TextArea;
		public static var FS_MODE_CHANGED:String = "fsModeChange";
		public static var SESSION_STARTED_CHANGED:String = "sessionStartedChange";
		public static var LESSON_FINISHED_CHANGED:String = "lessonFinishedChange";
		

		private var _fsMode:Boolean = false;
		[Bindable(Event=Main.FS_MODE_CHANGED)]
		public function set fsMode ( value : Boolean ) : void {
			_fsMode = value;
			dispatchEvent ( new Event ( Main.FS_MODE_CHANGED ));
		}
		public function get fsMode () : Boolean {
			return _fsMode;
		}
		
		private var _session_started:Boolean = true;
		[Bindable(Event=Main.SESSION_STARTED_CHANGED)]
		public function set session_started ( value : Boolean ) : void {
			_session_started = value;
			dispatchEvent ( new Event ( Main.SESSION_STARTED_CHANGED ));
		}
		
		public function get session_started ( ) : Boolean {
			return _session_started;
		}
		
		private var _lesson_finished:Boolean = true;
		[Bindable(Event=Main.LESSON_FINISHED_CHANGED)]
		public function set lesson_finished ( value : Boolean ) : void {
			_lesson_finished = value;
			dispatchEvent ( new Event ( Main.LESSON_FINISHED_CHANGED ));
		}
		
		public function get lesson_finished ( ) : Boolean {
			return _lesson_finished;
		}
		
		[Bindable]
		public var remainingTime:int = 0;
		
		[Bindable]
		public var totalTime:int = 60;
		
		private static var instance : Main;
		
		public function Main () {
		}
		
		public static function getInstance () : Main {
			if (instance == null){
				instance = new Main();
			}
			return instance;
		}
	}
}