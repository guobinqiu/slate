package Jili::Yiqifa::WebsiteCategoryGetRequest;
$VERSION = 0.0.1;

use warnings;
use strict;
use Moose;

#账号Key处请填写个人应用信息的key
has fields => ( is => 'rw', isa => 'Str' ); 
has type => ( is => 'rw', isa => 'Str' ); 

has apiParams=> ( is => 'rw', isa => 'HashRef[Str]' ); 

sub setFields {
    my ($self , $fields) = @_;
    $self->{fields} = $fields;
    $self->{apiParams}->{fields} = $fields;
}

sub setWtype {
    my ($self, $type) = @_;
    $self->{type} = $type;
    $self->{apiParams}->{type} = $type;
}

sub getApiMethodName {
    return "open.website.category.get";
}

sub getApiParams
{
    my( $self 	) = @_;
    return $self->{apiParams};
}

sub putOtherTextParam
{
    my ( $self, $key,$value) = @_;
    $self->{apiParams}->{$key} = $value;
    $self->{$key} = $value;
}
1;
