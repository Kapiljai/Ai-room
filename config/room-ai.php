<?php return ['driver'=>env('AI_DRIVER','mock'),'service_url'=>env('AI_SERVICE_URL','http://ai-service:8001'),'max_image_mb'=>(int)env('MAX_IMAGE_MB',10)];
