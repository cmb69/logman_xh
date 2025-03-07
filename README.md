# Logman_XH

Logman_XH facilitates the management of the CMSimple_XH log file,
which is used by the core and extensions to log relevant events.
While CMSimple_XH already facilitates *viewing* the contents of the log file
in the back-end, Logman_XH offers an enhanced user interface,
which allows to filter the log, and even to delete old or otherwise unnecessary entries.

## Table of Contents

  - [Requirements](#requirements)
  - [Download](#download)
  - [Installation](#installation)
  - [Settings](#settings)
  - [Usage](#usage)
  - [Troubleshooting](#troubleshooting)
  - [License](#license)
  - [Credits](#credits)

## Requirements

Logman_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 7.4.0.

## Download

The [lastest release](https://github.com/cmb69/logman_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1.  Backup the data on your server.
2.  Unzip the distribution on your computer.
3.  Upload the whole directory `logman/` to your server into the `plugins/`
    directory of CMSimple_XH.
4.  Set write permissions for the subdirectories `config/`, `css/` and
    `languages/`.
5.  Navigate to *Plugins*→*Logman* in the back-end to check if all
    requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH
plugins in the back-end of the website. Select *Plugins* → *Logman*.

You can change the default settings of Logman_XH under *Config*.
Hints for the options will be displayed when hovering over the help icon
with your mouse.

Localization is done under *Language*. You can translate the character
strings to your own language if there is no appropriate language file
available, or customize them according to your needs.

The look of Logman_XH can be customized under *Stylesheet*.

## Usage

In the back-end of your Website, go to *Plugins*→*Logman*→*Log file*
to see the oldest log entries according to the configuration setting
`entries_max`.
You can apply filters by using the search fields above the table,
to only display the log entries you are interested in.
For instance, you can filter by a certain module, or a category.
You can also remove all displayed log entries from the log file,
what is particularly useful after you have filtered by timestamp.

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/logman_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Logman_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Logman_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Logman_XH.  If not, see <http://www.gnu.org/licenses/>.

Copyright © 2025 Christoph M. Becker

## Credits

The plugin logo is from
[Log icons created by Freepik - Flaticon](https://www.flaticon.com/free-icons/log).
Many thanks for making this icon freely available.

And last but not least many thanks to [Peter Harteg](http://harteg.dk/),
the “father” of CMSimple,
and all developers of [CMSimple_XH](http://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
