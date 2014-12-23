# -*- coding: utf-8 -*-

import sys
import os

#sys.path.insert(0, os.path.abspath('.'))

#needs_sphinx = '1.0'
extensions = [
    'sphinx_http_domain'
]
templates_path = ['_templates']
source_suffix = '.rst'
#source_encoding = 'utf-8-sig'

master_doc = 'index'

project = u'Ouzo'
copyright = u'2014, Ouzo developers'

# The short X.Y version.
version = '1.3'
# The full version, including alpha/beta/rc tags.
release = '1.3'

#language = None

# There are two options for replacing |today|: either, you set today to some
# non-false value, then it is used:
#today = ''
# Else, today_fmt is used as the format for a strftime call.
#today_fmt = '%B %d, %Y'

exclude_patterns = ['_build']

#default_role = None
#add_function_parentheses = True
#add_module_names = True
#show_authors = False

pygments_style = 'sphinx'

#modindex_common_prefix = []
#keep_warnings = False


html_theme = 'default'
#html_theme_options = {}
#html_theme_path = []
#html_title = None
#html_short_title = None
#html_logo = None
#html_favicon = None
html_static_path = ['_static']
#html_extra_path = []
#html_last_updated_fmt = '%b %d, %Y'
#html_use_smartypants = True
#html_sidebars = {}
#html_additional_pages = {}
#html_domain_indices = True
#html_use_index = True
#html_split_index = False
#html_show_sourcelink = True
#html_show_sphinx = True
#html_show_copyright = True
#html_use_opensearch = ''
#html_file_suffix = None
htmlhelp_basename = 'Ouzodoc'


latex_elements = {
# The paper size ('letterpaper' or 'a4paper').
#'papersize': 'letterpaper',

# The font size ('10pt', '11pt' or '12pt').
#'pointsize': '10pt',

# Additional stuff for the LaTeX preamble.
#'preamble': '',
}

latex_documents = [
  ('index', 'Ouzo.tex', u'Ouzo Documentation',
   u'Ouzo developers', 'manual'),
]

#latex_logo = None
#latex_use_parts = False
#latex_show_pagerefs = False
#latex_show_urls = False
#latex_appendices = []

# If false, no module index is generated.
#latex_domain_indices = True


# -- Options for manual page output ---------------------------------------

# One entry per manual page. List of tuples
# (source start file, name, description, authors, manual section).
man_pages = [
    ('index', 'ouzo', u'Ouzo Documentation',
     [u'Ouzo developers'], 1)
]

# If true, show URL addresses after external links.
#man_show_urls = False


# -- Options for Texinfo output -------------------------------------------

# Grouping the document tree into Texinfo files. List of tuples
# (source start file, target name, title, author,
#  dir menu entry, description, category)
texinfo_documents = [
  ('index', 'Ouzo', u'Ouzo Documentation',
   u'Ouzo developers', 'Ouzo', 'One line description of project.',
   'Miscellaneous'),
]

# Documents to append as an appendix to all manuals.
#texinfo_appendices = []

# If false, no module index is generated.
#texinfo_domain_indices = True

# How to display URL addresses: 'footnote', 'no', or 'inline'.
#texinfo_show_urls = 'footnote'

# If true, do not generate a @detailmenu in the "Top" node's menu.
#texinfo_no_detailmenu = False

lexers['php'] = PhpLexer(startinline=True)
highlight_language='php'

on_rtd = os.environ.get('READTHEDOCS', None) == 'True'

if not on_rtd:  # only import and set the theme if we're building docs locally
    import sphinx_rtd_theme
    html_theme = 'sphinx_rtd_theme'
    html_theme_path = [sphinx_rtd_theme.get_html_theme_path()]
