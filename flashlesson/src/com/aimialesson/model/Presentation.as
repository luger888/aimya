package com.aimialesson.model
{
	import com.aimialesson.model.Main;
	
	import mx.collections.ArrayCollection;

	public class Presentation
	{
		
		private var fakeImages:Array = ["images/IMG_1120.JPG","images/IMG_1121.JPG","images/IMG_1122.JPG","images/IMG_1123.JPG","images/IMG_1124.JPG"];
		
		private var presentationImageUrls:ArrayCollection = new ArrayCollection(fakeImages);
		public function set imageUrls ( value : ArrayCollection ) : void {
			if (value) {
				presentationImageUrls = value;
			}
		}
		
		public function get imagesNumber () : int {
			return presentationImageUrls.length;
		}
		[Bindable]
		//public var currentImageURL:String;
		public function get currentImageURL () : String {
			return presentationImageUrls.getItemAt(currentImageN) as String;
		}
		
		public function set currentImageURL (value:String) : void {
			//debug("imitate of");
		}

		private var currentImageN:int = 0;
		public function set currentImageNumber ( value : int ) : void {
			if (value >= imagesNumber){
				currentImageN = 0;
			} else if (value < 0) {
				currentImageN = imagesNumber - 1; 
			} else {
				currentImageN = value;
			}
			debug("Presentation currentImageN:" + currentImageN);
			//just to update currentImageURL in viewvs 
			currentImageURL = "";
		}
		
		public function get currentImageNumber ( ) : int {
			return currentImageN;
		}
		
		
		public function Presentation()
		{
		}
		
		private static var instance:Presentation;
		public static function getInstance() : Presentation {
			if (instance == null){
				instance = new Presentation();
			}
			return instance;
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}