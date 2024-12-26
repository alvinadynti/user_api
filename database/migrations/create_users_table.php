// database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('name');
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
}
