package com.aimialesson.model 
{
	
	public class Client { 
		
		public function onBWCheck(... rest):Number { 
			return 0; 
		} 
		
		public function onBWDone(... rest):void { 
			var p_bw:Number; 
			if (rest.length > 0) p_bw = rest[0]; 
				// your application should do something here 
				// when the bandwidth check is complete 
				trace("bandwidth = " + p_bw + " Kbps."); 
		}  
}

}