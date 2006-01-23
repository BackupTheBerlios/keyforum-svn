#Tryico|Consente di ridurre nella traybar la finestra del core. Tasto dx per mostrarla
package tryico;
use strict;
use Win32::GUI;

my ($dos_window,$boh)=Win32::GUI::GetPerlWindow();
my $tray_icon=new Win32::GUI::Icon('keyforum.ico') or die("\nERROR: icon file not found!\n");
my $fake_win=new Win32::GUI::Window( -name    =>  'fakewin', -visible =>  0,);
#my $notify_icon = new Win32::GUI::NotifyIcon( $fake_win, -icon => $tray_icon, -id => 1,
#								-name => "kftray", -tip => "KeyForum running");

$fake_win->AddNotifyIcon(-icon => $tray_icon, -id => 1, -name => "tryico::kftray", -tip => "KeyForum running" );

sub kftray_Click {
	Win32::GUI::Hide($dos_window);
	return 1;
}

sub kftray_RightClick {
	Win32::GUI::Show($dos_window);
	return 1;
}

Win32::GUI::Hide($dos_window);
1;