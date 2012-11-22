package com.aimialesson.model
{
	import spark.components.TextArea;

	public class Main
	{
		public var debugger : TextArea;
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