<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;


function insertFileToS3Bucket($fileUrl,$sourceFile){
	$bucketName='your_bucket_name';
	
	try {
		$s3 = new S3Client([
			'version' => 'latest',
			'region'  => 'eu-central-1',
			'credentials' => [
				'key'    => "your_key",
				'secret' => "your_secret",
			]
		]);
		$result = $s3->putObject([
			'Bucket' => $bucketName,
			'Key'    => $fileUrl,
			'ACL'    => 'private',
			'SourceFile' => $sourceFile,
		]);
		return $result;
	} catch (S3Exception $e) {
		return $e->getMessage() . "\n";
	}
	

}