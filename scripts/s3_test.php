<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once APP_ROOT . '/includes/aws_s3.php';

try {
    $s3 = aws_s3_client();
    $bucket = aws_s3_bucket();

    // Prefer HeadBucket for a clearer error than doesBucketExist()
    $s3->headBucket(['Bucket' => $bucket]);

    echo "OK: S3 bucket accessible: {$bucket}\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "S3 test failed: " . get_class($e) . ': ' . $e->getMessage() . "\n");
    fwrite(STDERR, $e->getTraceAsString() . "\n");
    exit(1);
}

