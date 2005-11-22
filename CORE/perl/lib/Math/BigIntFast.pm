
###############################################################################
##                                                                           ##
##    Copyright (c) 2000 by Steffen Beyer.                                   ##
##    All rights reserved.                                                   ##
##                                                                           ##
##    This package is free software; you can redistribute it                 ##
##    and/or modify it under the same terms as Perl itself.                  ##
##                                                                           ##
###############################################################################

package Math::BigIntFast;

use strict;
use vars qw(@ISA @EXPORT @EXPORT_OK $VERSION %EXPORT_TAGS);

BEGIN # Re-export imports from Bit::Vector (if any):
{
    require Exporter;
    require Bit::Vector;
    @ISA         = qw(Exporter Bit::Vector);
    @EXPORT      = @Bit::Vector::EXPORT;
    @EXPORT_OK   = @Bit::Vector::EXPORT_OK;
    %EXPORT_TAGS = (all => [@EXPORT_OK]);
    $VERSION     = '6.0';
    Bit::Vector->import(@EXPORT,@EXPORT_OK);
}

1;

__END__

=head1 NAME

Math::BigIntFast - Efficient big integer arithmetic (in C)

=head1 SYNOPSIS

See L<Bit::Vector(3)> and L<Bit::Vector::Overload(3)>.

=head1 DESCRIPTION

See L<Bit::Vector(3)> and L<Bit::Vector::Overload(3)>.

=head1 SEE ALSO

Bit::Vector(3), Bit::Vector::Overload(3),
Set::IntRange(3), Math::MatrixBool(3),
Math::MatrixReal(3), DFA::Kleene(3),
Math::Kleene(3), Graph::Kruskal(3),
Math::Pari(3), PDL(3).

=head1 VERSION

This man page documents "Math::BigIntFast" version 6.0.

=head1 AUTHOR

  Steffen Beyer
  mailto:sb@engelschall.com
  http://www.engelschall.com/u/sb/download/

=head1 COPYRIGHT

Copyright (c) 2000 by Steffen Beyer. All rights reserved.

=head1 LICENSE

This package is free software; you can redistribute it and/or
modify it under the same terms as Perl itself, i.e., under the
terms of the "Artistic License" or the "GNU General Public License".

The C library at the core of this Perl module can additionally
be redistributed and/or modified under the terms of the "GNU
Library General Public License".

Please refer to the files "Artistic.txt", "GNU_GPL.txt" and
"GNU_LGPL.txt" in this distribution for details!

=head1 DISCLAIMER

This package is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

See the "GNU General Public License" for more details.

