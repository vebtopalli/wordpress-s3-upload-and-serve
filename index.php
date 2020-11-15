<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;


function insertFileToS3Bucket($fileUrl,$sourceFile){
	$bucketName=S3_BUCKET_NAME;
	
	try {
		$s3 = new S3Client([
			'version' => 'latest',
			'region'  => 'eu-central-1',
			'credentials' => [
				'key'    => S3_KEY,
				'secret' => S3_SECRET,
			]
		]);
		$result = $s3->putObject([
			'Bucket' => $bucketName,
			'Key'    => $fileUrl,
			'ACL'    => 'public-read',
			'SourceFile' => $sourceFile,
		]);
		return $result;
	} catch (S3Exception $e) {
		return $e->getMessage() . "\n";
	}
	

}