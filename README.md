# DigitalPersona Fingerprint Service Containerized

edit: /etc/ssl/openssl.cnf
1st line in the file added
openssl_conf = default_conf

End of file added

[default_conf]
ssl_conf = ssl_sect

[ssl_sect]
system_default = system_default_sect

[system_default_sect]
MinProtocol = TLSv1
CipherString = DEFAULT@SECLEVEL=1
Not 100% sure why i had to restart apache2 for it to take effect, but I had to.

systemctl restart apache2
reloaded the page and it works