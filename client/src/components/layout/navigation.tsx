import { Link, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/context/auth-context";
import { Bell, Menu, User } from "lucide-react";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

export function Navigation() {
  const { user, logout } = useAuth();
  const [location] = useLocation();

  const getNavItems = () => {
    if (!user) {
      return [
        { href: "/", label: "Home" },
        { href: "/help", label: "Help" },
      ];
    }

    switch (user.role) {
      case "admin":
        return [
          { href: "/admin", label: "Dashboard" },
          { href: "/admin/users", label: "Users" },
          { href: "/admin/operators", label: "Operators" },
        ];
      case "operator":
        return [
          { href: "/operator", label: "Dashboard" },
          { href: "/operator/buses", label: "Buses" },
          { href: "/operator/bookings", label: "Bookings" },
        ];
      case "agent":
        return [
          { href: "/agent", label: "Dashboard" },
        ];
      default:
        return [
          { href: "/", label: "Home" },
          { href: "/my-bookings", label: "My Bookings" },
          { href: "/help", label: "Help" },
        ];
    }
  };

  const navItems = getNavItems();

  return (
    <nav className="bg-white border-b border-gray-200 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex items-center">
            <div className="flex-shrink-0">
              <Link href="/" data-testid="link-home">
                <h1 className="text-2xl font-bold text-primary">MoBus</h1>
              </Link>
            </div>
            <div className="hidden md:ml-10 md:flex md:space-x-8">
              {navItems.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className={`${
                    location === item.href
                      ? "text-primary border-b-2 border-primary"
                      : "text-gray-500 hover:text-primary"
                  } px-3 py-2 text-sm font-medium transition-colors`}
                  data-testid={`link-${item.label.toLowerCase().replace(' ', '-')}`}
                >
                  {item.label}
                </Link>
              ))}
            </div>
          </div>
          <div className="flex items-center space-x-4">
            {user && (
              <Button variant="ghost" size="icon" data-testid="button-notifications">
                <Bell className="h-5 w-5" />
              </Button>
            )}
            
            {user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="icon" data-testid="button-user-menu">
                    <User className="h-5 w-5" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel data-testid="text-user-name">
                    {user.name || user.username}
                  </DropdownMenuLabel>
                  <DropdownMenuLabel className="text-xs text-muted-foreground font-normal" data-testid="text-user-role">
                    {user.role}
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem data-testid="text-profile">Profile</DropdownMenuItem>
                  <DropdownMenuItem data-testid="text-settings">Settings</DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={logout} data-testid="button-logout">
                    Logout
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <Link href="/login" data-testid="link-login">
                <Button>Login</Button>
              </Link>
            )}
            
            <Button variant="ghost" size="icon" className="md:hidden" data-testid="button-mobile-menu">
              <Menu className="h-5 w-5" />
            </Button>
          </div>
        </div>
      </div>
    </nav>
  );
}
