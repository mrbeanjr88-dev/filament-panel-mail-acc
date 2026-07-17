<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DNS;

class EmailProviderVerifier
{
    /**
     * MX record patterns mapped to known email providers.
     * Keys are provider identifiers, values are arrays of MX hostname patterns.
     */
    private const PROVIDER_FINGERPRINTS = [
        // === MAJOR GLOBAL EMAIL PROVIDERS ===
        'google' => [
            'patterns' => ['google.com', 'googlemail.com', 'gmail-smtp-in.l.google.com', 'alt1.gmail-smtp-in.l.google.com', 'alt2.gmail-smtp-in.l.google.com'],
            'label'    => 'Google Workspace / Gmail',
            'imap_host' => 'imap.gmail.com',
            'smtp_host' => 'smtp.gmail.com',
            'port'     => 993,
        ],
        'microsoft' => [
            'patterns' => ['outlook.com', 'microsoft.com', 'microsoft365.com', 'protection.outlook.com', 'mail.protection.outlook.com', 'outlook.office365.com'],
            'label'    => 'Microsoft 365 / Outlook',
            'imap_host' => 'outlook.office365.com',
            'smtp_host' => 'smtp.office365.com',
            'port'     => 993,
        ],
        'yahoo' => [
            'patterns' => ['yahoo.com', 'yahoodns.net', 'yahoomail.com'],
            'label'    => 'Yahoo Mail',
            'imap_host' => 'imap.mail.yahoo.com',
            'smtp_host' => 'smtp.mail.yahoo.com',
            'port'     => 993,
        ],
        'zoho' => [
            'patterns' => ['zoho.com', 'zoho.eu', 'zoho.in', 'zoho.com.au'],
            'label'    => 'Zoho Mail',
            'imap_host' => 'imap.zoho.com',
            'smtp_host' => 'smtp.zoho.com',
            'port'     => 993,
        ],
        'protonmail' => [
            'patterns' => ['protonmail.ch', 'proton.me', 'protonmail.com'],
            'label'    => 'ProtonMail',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'yandex' => [
            'patterns' => ['yandex.net', 'yandex.com', 'yandex.ru', 'yandex.рф'],
            'label'    => 'Yandex Mail',
            'imap_host' => 'imap.yandex.com',
            'smtp_host' => 'smtp.yandex.com',
            'port'     => 993,
        ],
        'icloud' => [
            'patterns' => ['icloud.com', 'me.com', 'mac.com'],
            'label'    => 'iCloud Mail',
            'imap_host' => 'imap.mail.me.com',
            'smtp_host' => 'smtp.mail.me.com',
            'port'     => 993,
        ],
        'fastmail' => [
            'patterns' => ['fastmail.com', 'fastmail.fm', 'messagingengine.com'],
            'label'    => 'Fastmail',
            'imap_host' => 'imap.fastmail.com',
            'smtp_host' => 'smtp.fastmail.com',
            'port'     => 993,
        ],
        'aol' => [
            'patterns' => ['aol.com', 'aim.com', 'aol.co.uk'],
            'label'    => 'AOL Mail',
            'imap_host' => 'imap.aol.com',
            'smtp_host' => 'smtp.aol.com',
            'port'     => 993,
        ],
        'tutanota' => [
            'patterns' => ['tutanota.com', 'tuta.io', 'tutanota.de'],
            'label'    => 'Tutanota / Tuta',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === US ISPs ===
        'comcast' => [
            'patterns' => ['comcast.net', 'xfinity.com'],
            'label'    => 'Comcast Xfinity',
            'imap_host' => 'imap.comcast.net',
            'smtp_host' => 'smtp.comcast.net',
            'port'     => 993,
        ],
        'att' => [
            'patterns' => ['att.net', 'sbcglobal.net', 'bellsouth.net', 'swbell.net', 'ameritech.net'],
            'label'    => 'AT&T / SBCGlobal',
            'imap_host' => 'imap.mail.att.net',
            'smtp_host' => 'smtp.mail.att.net',
            'port'     => 993,
        ],
        'virginmedia' => [
            'patterns' => ['virginmedia.com', 'blueyonder.co.uk', 'ntlworld.com', 'virgin.net'],
            'label'    => 'Virgin Media',
            'imap_host' => 'imap.virginmedia.com',
            'smtp_host' => 'smtp.virginmedia.com',
            'port'     => 993,
        ],

        // === GERMAN / AUSTRIAN / SWISS PROVIDERS ===
        'gmx' => [
            'patterns' => ['gmx.com', 'gmx.net', 'gmx.de', 'gmx.at', 'gmx.ch'],
            'label'    => 'GMX Mail',
            'imap_host' => 'imap.gmx.com',
            'smtp_host' => 'smtp.gmx.com',
            'port'     => 993,
        ],
        'web_de' => [
            'patterns' => ['web.de', 'email.de'],
            'label'    => 'WEB.DE',
            'imap_host' => 'imap.web.de',
            'smtp_host' => 'smtp.web.de',
            'port'     => 993,
        ],
        'ionos' => [
            'patterns' => ['ionos.com', '1and1.com', '1und1.de', 'ionos.de'],
            'label'    => 'IONOS Mail',
            'imap_host' => 'imap.ionos.com',
            'smtp_host' => 'smtp.ionos.com',
            'port'     => 993,
        ],
        'unitedinternet' => [
            'patterns' => ['united-internet.de'],
            'label'    => 'United Internet (GMX/WEB.DE/IONOS)',
            'imap_host' => 'imap.gmx.net',
            'smtp_host' => 'smtp.gmx.net',
            'port'     => 993,
        ],
        'telekom' => [
            'patterns' => ['t-online.de', 'telekom.de', 'magenta.at'],
            'label'    => 'Deutsche Telekom / T-Online',
            'imap_host' => 'securesmtp.t-online.de',
            'smtp_host' => 'securesmtp.t-online.de',
            'port'     => 587,
        ],
        'freenet' => [
            'patterns' => ['freenet.de', 'freenet.freenet.de'],
            'label'    => 'Freenet',
            'imap_host' => 'mx.freenet.de',
            'smtp_host' => 'mx.freenet.de',
            'port'     => 993,
        ],
        'vodafone_de' => [
            'patterns' => ['vodafonemail.de', 'vodafone.de', 'arcor.de', 'kabelmail.de', 'arcormail.de'],
            'label'    => 'Vodafone / Arcor',
            'imap_host' => 'imap.vodafonemail.de',
            'smtp_host' => 'smtp.vodafonemail.de',
            'port'     => 993,
        ],
        'mail_de' => [
            'patterns' => ['mail.de', 'mail.org'],
            'label'    => 'Mail.de',
            'imap_host' => 'imap.mail.de',
            'smtp_host' => 'smtp.mail.de',
            'port'     => 993,
        ],
        'netcologne' => [
            'patterns' => ['netcologne.de', 'nc-provider.de'],
            'label'    => 'NetCologne',
            'imap_host' => 'imap.netcologne.de',
            'smtp_host' => 'smtp.netcologne.de',
            'port'     => 993,
        ],
        'easybell' => [
            'patterns' => ['easybell.de'],
            'label'    => 'Easybell',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'strato' => [
            'patterns' => ['strato.de', 'strato.com', 'kundenserver.de', 'hosting.de'],
            'label'    => 'Strato / hosting.de',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hosteurope' => [
            'patterns' => ['hosteurope.de', 'hosteurope.com', 'mx0.hosteurope.de', 'mx1.hosteurope.de'],
            'label'    => 'HostEurope',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hetzner' => [
            'patterns' => ['hetzner.de', 'hetzner.com', 'hetzner.company'],
            'label'    => 'Hetzner',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'prohosting' => [
            'patterns' => ['prohosting.de', 'puretec.de', 'purehosting.de'],
            'label'    => 'ProHosting / Puretec',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'a1_austria' => [
            'patterns' => ['a1.net', 'smx01.a1.net', 'smx02.a1.net'],
            'label'    => 'A1 Telekom Austria',
            'imap_host' => 'imap.a1.net',
            'smtp_host' => 'smtp.a1.net',
            'port'     => 993,
        ],
        'drei_austria' => [
            'patterns' => ['drei.at', 'mail.drei.at'],
            'label'    => 'Drei Austria',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'swisscom' => [
            'patterns' => ['bluenet.ch', 'mx01.p.bluenet.ch', 'mx02.p.bluenet.ch'],
            'label'    => 'Swisscom / Bluewin',
            'imap_host' => 'imap.blufenet.ch',
            'smtp_host' => 'smtp.blufenet.ch',
            'port'     => 993,
        ],
        'kabel' => [
            'patterns' => ['kabel-deutschland.de', 'vodafone-kabel.de'],
            'label'    => 'Vodafone Kabel',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === ITALIAN PROVIDERS ===
        'libero' => [
            'patterns' => ['libero.it', 'libero.com', 'tin.it', 'tiscali.it'],
            'label'    => 'Libero / Tiscali',
            'imap_host' => 'imap.libero.it',
            'smtp_host' => 'smtp.libero.it',
            'port'     => 993,
        ],
        'tim_italy' => [
            'patterns' => ['mx.tim.it', 'tim.it', 'alice.it'],
            'label'    => 'TIM Italy',
            'imap_host' => 'imap.tim.it',
            'smtp_host' => 'smtp.tim.it',
            'port'     => 993,
        ],
        'virgilio' => [
            'patterns' => ['virgilio.it', 'smtp-in.virgilio.it'],
            'label'    => 'Virgilio Italy',
            'imap_host' => 'imap.virgilio.it',
            'smtp_host' => 'smtp.virgilio.it',
            'port'     => 993,
        ],
        'aruba' => [
            'patterns' => ['aruba.it', 'aruba.de', 'aruba.es', 'aruba.fr', 'aruba.com', 'mailfree.aruba.it', 'mx.aruba.it'],
            'label'    => 'Aruba / Aruba.it',
            'imap_host' => 'imaps.aruba.it',
            'smtp_host' => 'smtps.aruba.it',
            'port'     => 993,
        ],
        'omitech' => [
            'patterns' => ['omitech.it', 'ot-mail.it', 'mgw.omitech.it'],
            'label'    => 'Omitech Italy',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'informatica95' => [
            'patterns' => ['informatica95.info', 'informatica95.eu'],
            'label'    => 'Informatica95 Italy',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === BELGIAN / DUTCH PROVIDERS ===
        'proximus' => [
            'patterns' => ['proximus.be', 'glb.proximus.be', 'gtm-tc2.proximus.be'],
            'label'    => 'Proximus / Skynet Belgium',
            'imap_host' => 'imap.skynet.be',
            'smtp_host' => 'smtp.skynet.be',
            'port'     => 993,
        ],
        'telenet_be' => [
            'patterns' => ['telenet-ops.be', 'telenet.be'],
            'label'    => 'Telenet Belgium',
            'imap_host' => 'imap.telenet.be',
            'smtp_host' => 'smtp.telenet.be',
            'port'     => 993,
        ],
        'hetnet' => [
            'patterns' => ['hetnet.nl', 'kpnmail.nl', 'ziggo.nl', 'upcmail.nl'],
            'label'    => 'Dutch Providers',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === FRENCH PROVIDERS ===
        'free_fr' => [
            'patterns' => ['free.fr', 'mx1.free.fr', 'mx2.free.fr'],
            'label'    => 'Free France',
            'imap_host' => 'imap.free.fr',
            'smtp_host' => 'smtp.free.fr',
            'port'     => 993,
        ],
        'orange_fr' => [
            'patterns' => ['orange.fr', 'wanadoo.fr', 'smtp-in.orange.fr', 'smtp-in2.orange.fr'],
            'label'    => 'Orange / Wanadoo France',
            'imap_host' => 'imap.orange.fr',
            'smtp_host' => 'smtp.orange.fr',
            'port'     => 993,
        ],
        'sfr_fr' => [
            'patterns' => ['sfr.fr', 'numericable.fr', 'laposte.net'],
            'label'    => 'SFR / LaPoste France',
            'imap_host' => 'imap.sfr.fr',
            'smtp_host' => 'smtp.sfr.fr',
            'port'     => 993,
        ],

        // === EMAIL SECURITY / HOSTING PLATFORMS ===
        'sendgrid' => [
            'patterns' => ['sendgrid.net', 'sendgrid.com', 'sendgrid.org', 'mx.sendgrid.net'],
            'label'    => 'SendGrid (Transactional)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hornetsecurity' => [
            'patterns' => ['hornetsecurity.com', 'mx01.hornetsecurity.com', 'mx02.hornetsecurity.com'],
            'label'    => 'HornetSecurity (Spam Filter)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'antispameurope' => [
            'patterns' => ['antispameurope.com', 'hsmx01.antispameurope.com', 'hsmx02.antispameurope.com', 'hsmx03.antispameurope.com', 'mx19c.antispameurope.com', 'mx19d.antispameurope.com'],
            'label'    => 'AntiSpam Europe',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'abcpartner' => [
            'patterns' => ['abcpartner.de', 'mxsgg01.abcpartner.de', 'mxsgg02.abcpartner.de', 'mxsgg03.abcpartner.de'],
            'label'    => 'ABCpartner (Car Dealer Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'corpinter' => [
            'patterns' => ['corpinter.net', 'corpinter.de', 'mail-in.corpinter.net'],
            'label'    => 'CorpInter (Corporate Email)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'imago' => [
            'patterns' => ['imago.de', 'mx3.imago.de', 'mx9.imago.de'],
            'label'    => 'Imago (Automotive CRM)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'alserv' => [
            'patterns' => ['alserv.de', 'mx00.alserv.de'],
            'label'    => 'Alserv (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'cpanel' => [
            'patterns' => ['cpanel.net', 'cpanel.com', '邮件'],
            'label'    => 'cPanel / Shared Hosting',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'microsoft_hosting' => [
            'patterns' => ['secureserver.net', 'prodexmail.com', 'emailsvc.com'],
            'label'    => 'Microsoft / GoDaddy Hosting',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'sophos' => [
            'patterns' => ['sophos.com', 'hydra.sophos.com', 'mx-01-eu-central-1.prod.hydra.sophos.com'],
            'label'    => 'Sophos Email Security',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'trendmicro' => [
            'patterns' => ['trendmicro.eu', 'trendmicro.com', 'tmes.trendmicro.eu', 'in.tmes.trendmicro.eu'],
            'label'    => 'Trend Micro Email Security',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'proofpoint' => [
            'patterns' => ['pphosted.com', 'ppe-hosted.com', 'gslb.pphosted.com'],
            'label'    => 'Proofpoint (Email Security)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'cisco_ironport' => [
            'patterns' => ['iphmx.com', 'c3s2.iphmx.com'],
            'label'    => 'Cisco IronPort',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'nospamproxy' => [
            'patterns' => ['nospamproxy.com', 'cloud.nospamproxy.com'],
            'label'    => 'NoSpamProxy (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'allinkl' => [
            'patterns' => ['kasserver.com', 'all-inkl.com'],
            'label'    => 'all-inkl.com / KaSERVER',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'rox' => [
            'patterns' => ['rox.net'],
            'label'    => 'ROX (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'tsystems' => [
            'patterns' => ['t-systems-service.com', 't-systems.com', 'mas.t-systems-service.com'],
            'label'    => 'T-Systems (Deutsche Telekom)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mars_services' => [
            'patterns' => ['mars-services.de', 'nsp.mars-services.de'],
            'label'    => 'Mars Services (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'rzone' => [
            'patterns' => ['rzone.de', 'smtpin.rzone.de'],
            'label'    => 'rzone.de (Strato)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'udag' => [
            'patterns' => ['udag.de', 'mx00.udag.de', 'mx01.udag.de'],
            'label'    => 'UDAG (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'avag' => [
            'patterns' => ['avag.eu', 'mx1.avag.eu'],
            'label'    => 'AVAG (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'cablelink' => [
            'patterns' => ['cablelink.at', 'mx-in.cablelink.at'],
            'label'    => 'Cablelink Austria',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'nshosting' => [
            'patterns' => ['nshosting.biz', 'nshosting.de'],
            'label'    => 'NSHosting (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'vapn' => [
            'patterns' => ['vapn.de', 'ssx02.vapn.de'],
            'label'    => 'VAPN (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'capa24' => [
            'patterns' => ['capa24.de', 'mail.capa24.de'],
            'label'    => 'Capa24 (Automotive Marketing)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'autogator' => [
            'patterns' => ['autogator.de', 'post.autogator.de'],
            'label'    => 'Autogator (Automotive Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'autodo' => [
            'patterns' => ['autodo.eu', 'mail.autodo.eu'],
            'label'    => 'Autodo (Automotive CRM)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'webmobil24' => [
            'patterns' => ['webmobil24.com', 'mail.webmobil24.com'],
            'label'    => 'webmobil24 (Car Marketplace)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'tri_ag' => [
            'patterns' => ['tri.ag', 'mail01.tri.ag'],
            'label'    => 'TRI (German Automotive)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ewetel' => [
            'patterns' => ['ewetel.de', 'ewetel.net', 'mx1.ewetel.de', 'mx-x1.ewetel.de'],
            'label'    => 'Ewetel (German ISP)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'fortimail' => [
            'patterns' => ['fortimailcloud.com', 'mail-1.fortimailcloud.com'],
            'label'    => 'FortiMail Cloud (Fortinet)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === EMAIL SECURITY GATEWAYS ===
        'barracuda' => [
            'patterns' => ['barracudanetworks.com', 'barracuda.com'],
            'label'    => 'Barracuda Networks',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mimecast' => [
            'patterns' => ['mimecast.com'],
            'label'    => 'Mimecast',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'spamexperts' => [
            'patterns' => ['spamexperts.com', 'spamexperts.net', 'spamexperts.eu'],
            'label'    => 'SpamExperts',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'secure_mailgate' => [
            'patterns' => ['secure-mailgate.com', 'secure-mailgateway.com'],
            'label'    => 'Secure Mailgate (German Gateway)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'antispamcloud' => [
            'patterns' => ['antispamcloud.com'],
            'label'    => 'AntispamCloud (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mailspamprotection' => [
            'patterns' => ['mailspamprotection.com'],
            'label'    => 'MailSpamProtection',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hostedmxserver' => [
            'patterns' => ['hostedmxserver.com'],
            'label'    => 'HostedMXServer',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'dts_security' => [
            'patterns' => ['dts-security.de'],
            'label'    => 'DTS Security (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'expurgate' => [
            'patterns' => ['expurgate.net'],
            'label'    => 'Expurgate (German Email Security)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mymailwall' => [
            'patterns' => ['mymailwall.com'],
            'label'    => 'MyMailWall',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'h_email' => [
            'patterns' => ['h-email.net'],
            'label'    => 'H-Email (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'secure_shield' => [
            'patterns' => ['secure-shield.at'],
            'label'    => 'Secure Shield (Austrian)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mailfrau' => [
            'patterns' => ['mailfrau.de'],
            'label'    => 'Mailfrau (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mtaroutes' => [
            'patterns' => ['mtaroutes.com'],
            'label'    => 'MTA Routes',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === HOSTING / INFRASTRUCTURE ===
        'one_com' => [
            'patterns' => ['one.com'],
            'label'    => 'One.com (Scandinavian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ispgateway' => [
            'patterns' => ['ispgateway.de'],
            'label'    => 'ISPGateway (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'your_server' => [
            'patterns' => ['your-server.de'],
            'label'    => 'Your-Server.de (Hetzner)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'easyname' => [
            'patterns' => ['easyname.eu'],
            'label'    => 'Easyname (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ovh' => [
            'patterns' => ['ovh.net', 'ovh.com', 'ovhcloud.com'],
            'label'    => 'OVHcloud',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'jimdo' => [
            'patterns' => ['jimdo.com'],
            'label'    => 'Jimdo (Website Builder)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'cloudflare_routing' => [
            'patterns' => ['cloudflare.net', 'cloudflare.com'],
            'label'    => 'Cloudflare Email Routing',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'goserver' => [
            'patterns' => ['goserver.host'],
            'label'    => 'GoServer (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hostpoint' => [
            'patterns' => ['hostpoint.ch'],
            'label'    => 'Hostpoint (Swiss Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'netcup' => [
            'patterns' => ['netcup.net'],
            'label'    => 'NetCup (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hostinger' => [
            'patterns' => ['hostinger.com', 'hostinger.de'],
            'label'    => 'Hostinger',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'schlund' => [
            'patterns' => ['schlund.de'],
            'label'    => 'Schlund (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'hostnet_nl' => [
            'patterns' => ['hostnet.nl'],
            'label'    => 'Hostnet (Dutch Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'register_it' => [
            'patterns' => ['register.it'],
            'label'    => 'Register.it (Italian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'goneo' => [
            'patterns' => ['goneo.de'],
            'label'    => 'goneo (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mailbox_org' => [
            'patterns' => ['mailbox.org'],
            'label'    => 'Mailbox.org (German Email)',
            'imap_host' => 'imap.mailbox.org',
            'smtp_host' => 'smtp.mailbox.org',
            'port'     => 993,
        ],
        'hostedoffice' => [
            'patterns' => ['hostedoffice.ag'],
            'label'    => 'Hosted Office (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mailplatform' => [
            'patterns' => ['mailplatform.eu'],
            'label'    => 'MailPlatform (EU)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === GERMAN REGIONAL / SPECIALIZED ===
        'agenturserver' => [
            'patterns' => ['agenturserver.de'],
            'label'    => 'Agenturserver (German Agency Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'gdnet' => [
            'patterns' => ['gdnet.de'],
            'label'    => 'GDNet (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ronet' => [
            'patterns' => ['ronet.de'],
            'label'    => 'Ronet (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'infoserve' => [
            'patterns' => ['infoserve.de'],
            'label'    => 'InfoServe (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'osn' => [
            'patterns' => ['osn.de'],
            'label'    => 'OSN (German Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'sss_edv' => [
            'patterns' => ['sss-edv.de'],
            'label'    => 'SSS-EDV (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'fizon' => [
            'patterns' => ['fizon.de'],
            'label'    => 'Fizon (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'rundrweb' => [
            'patterns' => ['rundrweb.com'],
            'label'    => 'RundR Web (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'centaur' => [
            'patterns' => ['centaur.de'],
            'label'    => 'Centaur (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ahponline' => [
            'patterns' => ['ahponline.de'],
            'label'    => 'AHP Online (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'work_de' => [
            'patterns' => ['work.de', 'mailscanner.work.de'],
            'label'    => 'Work.de (German)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mymagenta_business' => [
            'patterns' => ['mymagenta.business', 'mymagenta.de'],
            'label'    => 'Telekom Business (mymagenta)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === AUSTRIAN / SWISS / BELGIAN / DUTCH ===
        'bon_at' => [
            'patterns' => ['bon.at'],
            'label'    => 'BON (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ebit' => [
            'patterns' => ['ebit.at'],
            'label'    => 'EBIT (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mynet_at' => [
            'patterns' => ['mynet.at'],
            'label'    => 'MyNet (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'kabsi' => [
            'patterns' => ['kabsi.at'],
            'label'    => 'Kabsi (Austrian Hosting)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'mailprotect_be' => [
            'patterns' => ['mailprotect.be'],
            'label'    => 'MailProtect (Belgian)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === ITALIAN SPECIALIZED ===
        'arubabusiness' => [
            'patterns' => ['arubabusiness.it'],
            'label'    => 'Aruba Business Italy',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === CORPORATE / OEM ===
        'renault_corp' => [
            'patterns' => ['renault.fr', 'smtp.renault.fr', 'smtp2.renault.fr'],
            'label'    => 'Renault Corporate',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'skoda_corp' => [
            'patterns' => ['skoda-auto.de', 'mail.skoda-auto.de'],
            'label'    => 'Skoda Corporate',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],
        'ihk' => [
            'patterns' => ['ihk.de'],
            'label'    => 'IHK (German Chambers of Commerce)',
            'imap_host' => null,
            'smtp_host' => null,
            'port'     => null,
        ],

        // === CHINESE / ASIAN PROVIDERS ===
        'qq' => [
            'patterns' => ['qq.com', 'foxmail.com', '163.com', '126.com', 'sina.com', 'netease.com', 'mxmail.netease.com'],
            'label'    => 'QQ / 163 / NetEase (China)',
            'imap_host' => 'imap.qq.com',
            'smtp_host' => 'smtp.qq.com',
            'port'     => 993,
        ],
        'naver' => [
            'patterns' => ['naver.com', 'daum.net', 'hanmail.net', 'kakao.com'],
            'label'    => 'Naver / Daum (Korea)',
            'imap_host' => 'imap.naver.com',
            'smtp_host' => 'smtp.naver.com',
            'port'     => 993,
        ],
        'mail_ru' => [
            'patterns' => ['mail.ru', 'bk.ru', 'inbox.ru', 'list.ru'],
            'label'    => 'Mail.ru',
            'imap_host' => 'imap.mail.ru',
            'smtp_host' => 'smtp.mail.ru',
            'port'     => 993,
        ],
        'mail_ru_other' => [
            'patterns' => ['i.ua', 'ukr.net', 'meta.ua'],
            'label'    => 'Ukrainian Providers',
            'imap_host' => 'mail.ukr.net',
            'smtp_host' => 'smtp.ukr.net',
            'port'     => 993,
        ],

        // === OTHER EUROPEAN ===
        'mail_eu' => [
            'patterns' => ['mail.ee', 'mail.lv'],
            'label'    => 'Mail.ee (Baltics)',
            'imap_host' => 'imap.mail.ee',
            'smtp_host' => 'smtp.mail.ee',
            'port'     => 993,
        ],
        'seznam' => [
            'patterns' => ['seznam.cz', 'email.cz', 'post.cz'],
            'label'    => 'Seznam (Czech)',
            'imap_host' => 'imap.seznam.cz',
            'smtp_host' => 'smtp.seznam.cz',
            'port'     => 993,
        ],
        'mail_com' => [
            'patterns' => ['mail.com', 'email.com', 'email.cz'],
            'label'    => 'Mail.com / Email.com',
            'imap_host' => 'imap.mail.com',
            'smtp_host' => 'smtp.mail.com',
            'port'     => 993,
        ],

        // === BRAZILIAN / LATAM ===
        'uol' => [
            'patterns' => ['uol.com.br', 'bol.com.br', 'uol.com'],
            'label'    => 'UOL / Bol',
            'imap_host' => 'imap.uol.com.br',
            'smtp_host' => 'smtp.uol.com.br',
            'port'     => 993,
        ],
        'terra' => [
            'patterns' => ['terra.com.br', 'terra.com', 'terra.es'],
            'label'    => 'Terra',
            'imap_host' => 'imap.terra.com.br',
            'smtp_host' => 'smtp.terra.com.br',
            'port'     => 993,
        ],
        'rediffmail' => [
            'patterns' => ['rediffmail.com', 'rediff.com'],
            'label'    => 'Rediffmail',
            'imap_host' => 'imap.rediffmail.com',
            'smtp_host' => 'smtp.rediffmail.com',
            'port'     => 993,
        ],
        'ozemail' => [
            'patterns' => ['ozemail.com.au', 'bigpond.com', 'bigpond.net.au', 'telstra.com'],
            'label'    => 'Telstra / OzEmail',
            'imap_host' => 'imap.ozemail.com.au',
            'smtp_host' => 'smtp.ozemail.com.au',
            'port'     => 993,
        ],
    ];

    /**
     * Get the domain from an email address.
     */
    public function extractDomain(string $email): ?string
    {
        $parts = explode('@', trim($email));
        return count($parts) === 2 ? strtolower(trim($parts[1])) : null;
    }

    /**
     * Perform DNS MX lookup for a domain with caching.
     *
     * @return array{mx_records: array, mx_hosts: array, priority_map: array}
     */
    public function lookupMx(string $domain): array
    {
        $cacheKey = "mx_lookup_{$domain}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain) {
            $mxRecords = [];
            $mxHosts = [];
            $priorityMap = [];

            // Try getmxrr first (Linux/macOS) — suppress warnings for rate-limited DNS
            if (function_exists('getmxrr')) {
                $mxHostsRaw = [];
                $mxWeightsRaw = [];
                $success = @getmxrr($domain, $mxHostsRaw, $mxWeightsRaw);

                if ($success && count($mxHostsRaw) > 0) {
                    foreach ($mxHostsRaw as $i => $host) {
                        $priority = $mxWeightsRaw[$i] ?? 10;
                        $mxRecords[] = [
                            'host'     => strtolower(trim($host, '.')),
                            'priority' => (int) $priority,
                        ];
                        $priorityMap[strtolower(trim($host, '.'))] = (int) $priority;
                    }
                    usort($mxRecords, fn($a, $b) => $a['priority'] <=> $b['priority']);
                    $mxHosts = array_column($mxRecords, 'host');
                }
            }

            // Fallback: use dns_get_record (wrapped in error suppression for rate limits)
            if (empty($mxHosts)) {
                try {
                    $records = @dns_get_record($domain, DNS_MX);
                    if (!empty($records)) {
                        foreach ($records as $record) {
                            $mxRecords[] = [
                                'host'     => strtolower(trim($record['target'] ?? '', '.')),
                                'priority' => (int) ($record['pri'] ?? 10),
                            ];
                            $priorityMap[strtolower(trim($record['target'] ?? '', '.'))] = (int) ($record['pri'] ?? 10);
                        }
                        usort($mxRecords, fn($a, $b) => $a['priority'] <=> $b['priority']);
                        $mxHosts = array_column($mxRecords, 'host');
                    }
                } catch (\Throwable $e) {
                    // DNS rate limit or transient failure — skip
                }
            }

            // Fallback: try A record if no MX
            if (empty($mxHosts)) {
                try {
                    $aRecords = @dns_get_record($domain, DNS_A);
                    if (!empty($aRecords)) {
                        $mxRecords[] = [
                            'host'     => $domain,
                            'priority' => 0,
                        ];
                        $mxHosts[] = $domain;
                        $priorityMap[$domain] = 0;
                    }
                } catch (\Throwable $e) {
                    // DNS rate limit or transient failure — skip
                }
            }

            return [
                'mx_records'    => $mxRecords,
                'mx_hosts'      => $mxHosts,
                'priority_map'  => $priorityMap,
            ];
        });
    }

    /**
     * Detect the email provider based on MX records.
     *
     * @return array{provider: string, label: string, confidence: float, imap_host: ?string, smtp_host: ?string, port: ?int}
     */
    public function detectProvider(array $mxHosts): array
    {
        $scores = [];

        foreach (self::PROVIDER_FINGERPRINTS as $providerId => $providerData) {
            $score = 0;
            foreach ($mxHosts as $mxHost) {
                foreach ($providerData['patterns'] as $pattern) {
                    if (str_contains($mxHost, $pattern)) {
                        // Primary MX gets higher weight
                        $score += ($mxHost === reset($mxHosts)) ? 10 : 5;
                        break;
                    }
                }
            }
            if ($score > 0) {
                $scores[$providerId] = $score;
            }
        }

        if (empty($scores)) {
            return [
                'provider'   => 'unknown',
                'label'      => 'Unknown / Custom',
                'confidence' => 0.0,
                'imap_host'  => null,
                'smtp_host'  => null,
                'port'       => null,
            ];
        }

        arsort($scores);
        $topProvider = array_key_first($scores);
        $topScore = $scores[$topProvider];

        // Calculate confidence (0-1)
        $maxPossible = count($mxHosts) * 10;
        $confidence = min(1.0, $topScore / max(1, $maxPossible));

        $providerData = self::PROVIDER_FINGERPRINTS[$topProvider];

        return [
            'provider'   => $topProvider,
            'label'      => $providerData['label'],
            'confidence' => round($confidence, 2),
            'imap_host'  => $providerData['imap_host'],
            'smtp_host'  => $providerData['smtp_host'],
            'port'       => $providerData['port'],
        ];
    }

    /**
     * Verify if an email address is reachable via SMTP (RCPT TO check).
     * Uses the primary MX server for the domain.
     *
     * @return array{reachable: bool, mx_host: string, smtp_banner: string, error: ?string, response_code: int}
     */
    public function smtpVerify(string $email, ?string $mxHost = null, int $timeout = 10): array
    {
        $domain = $this->extractDomain($email);
        if (!$domain) {
            return [
                'reachable'    => false,
                'mx_host'      => '',
                'smtp_banner'  => '',
                'error'        => 'Invalid email format',
                'response_code' => 0,
            ];
        }

        if (!$mxHost) {
            $mxData = $this->lookupMx($domain);
            $mxHost = $mxData['mx_hosts'][0] ?? null;
        }

        if (!$mxHost) {
            return [
                'reachable'    => false,
                'mx_host'      => '',
                'smtp_banner'  => '',
                'error'        => 'No MX record found for ' . $domain,
                'response_code' => 0,
            ];
        }

        try {
            $errno = 0;
            $errstr = '';
            $fp = @stream_socket_client(
                "tcp://{$mxHost}:25",
                $errno,
                $errstr,
                $timeout
            );

            if (!$fp) {
                return [
                    'reachable'    => false,
                    'mx_host'      => $mxHost,
                    'smtp_banner'  => '',
                    'error'        => "Connection failed: {$errstr} ({$errno})",
                    'response_code' => 0,
                ];
            }

            // Read banner
            $banner = @fgets($fp, 512);

            // EHLO
            @fwrite($fp, "EHLO verify.check\r\n");
            @stream_set_timeout($fp, $timeout);
            $ehloResponse = '';
            while ($line = @fgets($fp, 512)) {
                $ehloResponse .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }

            // MAIL FROM
            @fwrite($fp, "MAIL FROM:<verify@" . $domain . ">\r\n");
            $mailFromResponse = @fgets($fp, 512);

            // RCPT TO
            @fwrite($fp, "RCPT TO:<{$email}>\r\n");
            $rcptResponse = @fgets($fp, 512);

            // QUIT
            @fwrite($fp, "QUIT\r\n");
            @fclose($fp);

            $responseCode = (int) substr(trim($rcptResponse ?? ''), 0, 3);

            // 250 = accepted, 550/551/552/553 = rejected
            $reachable = in_array($responseCode, [250, 251, 252]);

            return [
                'reachable'    => $reachable,
                'mx_host'      => $mxHost,
                'smtp_banner'  => trim($banner ?? ''),
                'error'        => $reachable ? null : trim($rcptResponse ?? 'Unknown error'),
                'response_code' => $responseCode,
            ];
        } catch (\Exception $e) {
            return [
                'reachable'    => false,
                'mx_host'      => $mxHost,
                'smtp_banner'  => '',
                'error'        => $e->getMessage(),
                'response_code' => 0,
            ];
        }
    }

    /**
     * Full verification for a single email: MX lookup + provider detection + optional SMTP check.
     *
     * @return array{
     *     email: string,
     *     domain: string,
     *     provider: string,
     *     provider_label: string,
     *     confidence: float,
     *     imap_host: ?string,
     *     smtp_host: ?string,
     *     port: ?int,
     *     mx_records: array,
     *     smtp_reachable: ?bool,
     *     smtp_response_code: ?int,
     *     smtp_error: ?string,
     *     verification_time_ms: int
     * }
     */
    public function verify(string $email, bool $smtpCheck = false): array
    {
        $start = microtime(true);
        $domain = $this->extractDomain($email);

        if (!$domain) {
            return array_merge($this->emptyResult($email), [
                'error' => 'Invalid email format',
            ]);
        }

        // Step 1: MX lookup
        $mxData = $this->lookupMx($domain);

        // Step 2: Provider detection
        $providerData = $this->detectProvider($mxData['mx_hosts']);

        // Step 3: SMTP verification (optional)
        $smtpResult = null;
        if ($smtpCheck) {
            $smtpResult = $this->smtpVerify($email, $mxData['mx_hosts'][0] ?? null);
        }

        $elapsed = (int) ((microtime(true) - $start) * 1000);

        return [
            'email'              => $email,
            'domain'             => $domain,
            'provider'           => $providerData['provider'],
            'provider_label'     => $providerData['label'],
            'confidence'         => $providerData['confidence'],
            'imap_host'          => $providerData['imap_host'],
            'smtp_host'          => $providerData['smtp_host'],
            'port'               => $providerData['port'],
            'mx_records'         => $mxData['mx_records'],
            'smtp_reachable'     => $smtpResult['reachable'] ?? null,
            'smtp_response_code' => $smtpResult['response_code'] ?? null,
            'smtp_error'         => $smtpResult['error'] ?? null,
            'verification_time_ms' => $elapsed,
        ];
    }

    /**
     * Bulk verify a list of emails.
     * Returns results grouped by provider.
     *
     * @param  array  $emails  Array of email addresses
     * @param  bool   $smtpCheck  Whether to perform SMTP verification
     * @return array{results: array, summary: array, errors: array}
     */
    public function verifyBulk(array $emails, bool $smtpCheck = false): array
    {
        $results = [];
        $summary = [];
        $errors = [];
        $seenDomains = [];

        // Deduplicate emails
        $uniqueEmails = array_unique(array_map('strtolower', array_map('trim', $emails)));

        foreach ($uniqueEmails as $email) {
            try {
                $result = $this->verify($email, $smtpCheck);
                $results[] = $result;

                // Count by provider
                $provider = $result['provider'];
                $summary[$provider] = ($summary[$provider] ?? 0) + 1;

            } catch (\Exception $e) {
                $errors[] = [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Sort summary by count descending
        arsort($summary);

        return [
            'results' => $results,
            'summary' => $summary,
            'errors'  => $errors,
            'total'   => count($uniqueEmails),
        ];
    }

    /**
     * Get all known providers with their metadata.
     */
    public static function getKnownProviders(): array
    {
        return self::PROVIDER_FINGERPRINTS;
    }

    /**
     * Parse a CSV file and extract email addresses from a specific column.
     *
     * @return array{emails: array, total_lines: int, invalid_lines: int}
     */
    public function parseCsvEmails(string $filePath, string $emailColumn = 'email', string $delimiter = ','): array
    {
        $emails = [];
        $totalLines = 0;
        $invalidLines = 0;

        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Cannot open file: {$filePath}");
        }

        // Read header
        $header = fgetcsv($handle, 0, $delimiter);
        if ($header === false) {
            fclose($handle);
            throw new \RuntimeException("Cannot read CSV header");
        }

        // Find email column index
        $emailIdx = array_search(strtolower($emailColumn), array_map('strtolower', $header));
        if ($emailIdx === false) {
            // Try common column names
            foreach (['email', 'mail', 'e-mail', 'adresse', 'correo'] as $col) {
                $idx = array_search($col, array_map('strtolower', $header));
                if ($idx !== false) {
                    $emailIdx = $idx;
                    break;
                }
            }
            if ($emailIdx === false) {
                fclose($handle);
                throw new \RuntimeException("Email column '{$emailColumn}' not found in CSV header: " . implode(', ', $header));
            }
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $totalLines++;
            $email = trim($row[$emailIdx] ?? '');

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            } else {
                $invalidLines++;
            }
        }

        fclose($handle);

        return [
            'emails'       => $emails,
            'total_lines'  => $totalLines,
            'invalid_lines' => $invalidLines,
        ];
    }

    /**
     * Parse a plain text file (one email per line).
     */
    public function parseTxtEmails(string $filePath): array
    {
        $emails = [];
        $totalLines = 0;
        $invalidLines = 0;

        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $totalLines++;
            $email = trim($line);

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            } else {
                $invalidLines++;
            }
        }

        return [
            'emails'       => $emails,
            'total_lines'  => $totalLines,
            'invalid_lines' => $invalidLines,
        ];
    }

    private function emptyResult(string $email): array
    {
        return [
            'email'              => $email,
            'domain'             => $this->extractDomain($email),
            'provider'           => 'unknown',
            'provider_label'     => 'Unknown',
            'confidence'         => 0.0,
            'imap_host'          => null,
            'smtp_host'          => null,
            'port'               => null,
            'mx_records'         => [],
            'smtp_reachable'     => null,
            'smtp_response_code' => null,
            'smtp_error'         => null,
            'verification_time_ms' => 0,
        ];
    }
}
