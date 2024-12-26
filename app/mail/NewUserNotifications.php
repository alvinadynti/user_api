public function __construct(User $user)
{
    $this->user = $user;
}

public function build()
{
    return $this->subject('New User Registered')->view('emails.new_user_notification');
}
