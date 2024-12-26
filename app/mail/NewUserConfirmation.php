public function __construct(User $user)
{
    $this->user = $user;
}

public function build()
{
    return $this->subject('Account Created')->view('emails.new_user_confirmation');
}
