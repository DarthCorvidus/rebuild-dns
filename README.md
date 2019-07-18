# rebuild-dns
Recreates dnsmasq host entries for IPv6 after prefix changes.


When I got my first dual stack internet connection, I configured dnsmasq to use
public IPv6 addresses for my home network. This, however, led to some problems
as soon as my internet provider would change my IPv6 prefix: all host entries
where broken, as they pointed to addresses within another network.

Also, I found it tedious to maintain both a list of IPv4 ←→ hostnames as well
as a list of MAC addresses ←→ IPv4 adresses as well. There are about 12 machines
within my home network; it is sufficient to have a 1:1 relation between MAC
addresses, IPv4 addresses and hostnames.

Therefore, I started to keep a file containing this relation, such as:

	ab:cd:ef:00:01:02:03:04	192.168.0.1	fileserver

Then I wrote this script, rebuild-dns.php, which would (re)create a dhcp table,
an IPv4 hosts as well as an IPv6 hosts. rebuild-dns.php will use SLAAC to create
IPv6 addresses using the MAC address laid down in aforementioned table;
obviously, you'll have to configure your hosts to use SLAAC.
