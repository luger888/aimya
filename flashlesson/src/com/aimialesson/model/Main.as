package com.aimialesson.model
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	import spark.components.TextArea;

	public class Main extends EventDispatcher
	{
		public var debugger : TextArea;
		public static var FS_MODE_CHANGED:String = "fsModeChange";
		

		private var _fsMode:Boolean = false;
		[Bindable(Event=Main.FS_MODE_CHANGED)]
		public function set fsMode ( value : Boolean ) : void {
			_fsMode = value;
			dispatchEvent ( new Event ( Main.FS_MODE_CHANGED ));
		}
		public function get fsMode () : Boolean {
			return _fsMode;
		}
		[Bindable]
		public var session_started:Boolean = false;
		
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