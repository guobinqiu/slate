package Wenwen::Email;

use common::sense;

#use diagnostics -verbose;
use Wenwen::Config;
use Email::MIME;
use Email::Sender::Simple qw(sendmail);

sub new {
    my $class = shift;
    my $self  = {};
    if (bless($self, $class)->init(@_)) {
        return $self;
    }
    else {
        # throw some sort of error
    }
}

sub init {

    1;
}

sub send {

    # Todo isolate dsn etc. from here
    my ($self, $to, $subject, $body) = @_;

    my $email_config = c->param('email');
    my $from         = $email_config->{from};
    say "From: $from";
    say "To: $to";
    say "Subject: $subject";
    say "body: $body";

    my $message = Email::MIME->create(
        header_str => [
            From    => $from,
            To      => $to,
            Subject => $subject,
        ],
        attributes => {
            encoding => 'quoted-printable',
            charset  => 'ISO-8859-1',
        },
        body_str => $body,
    );
    sendmail($message);
}

1;
