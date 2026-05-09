<?php
declare(strict_types=1);

use Aws\S3\S3Client;

function aws_s3_client(): S3Client
{
    $region = AWS_REGION;
    $key = AWS_ACCESS_KEY_ID;
    $secret = AWS_SECRET_ACCESS_KEY;

    if ($region === '' || $key === '' || $secret === '') {
        throw new RuntimeException('Missing AWS S3 config. Set AWS_REGION, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY in .env.');
    }

    return new S3Client([
        'version' => 'latest',
        'region' => $region,
        'credentials' => [
            'key' => $key,
            'secret' => $secret,
        ],
    ]);
}

function aws_s3_bucket(): string
{
    $b = AWS_S3_BUCKET;
    if ($b === '') {
        throw new RuntimeException('Missing AWS_S3_BUCKET in .env.');
    }
    return $b;
}

