@servers(['web' => 'ubuntu@119.29.173.208'])

@task('foo', ['on' => 'web'])
cd /data/
ls -la
@endtask