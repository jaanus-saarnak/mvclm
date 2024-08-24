<?php
/**
 * MvcLM v1.0 - MVC Framework with Login Manager
 * Copyright (c) 2021 Jaanus Saarnak
 *
 * LICENSE AND DISCLAIMER
 *
 * THIS SOFTWARE CAN BE USED ON ONE DOMAIN FOR TESTING USE ONLY.
 * ALL OTHER RIGHTS ARE RESERVED.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Software website: https://www.MvcLM.com
 */

namespace Core;

use App\Flash;


class View
{
    /**
     * @param string $template
     * @param array $content
     * @throws \Exception
     */
    public static function renderTemplate($template, $content = [])
    {
        $content['flash_messages'] = Flash::getMessages();

        $file = CONFIG['path'] . $template;

        if (is_readable($file)) {

            require_once($file);

        } else {

            throw new \Exception($file . " not found");
        }
    }


    /**
     * @param array $pageLinks
     * @return mixed
     */
    static function renderPageLinks($pageLinks)
    {
        if (is_array($pageLinks) && count($pageLinks) > 1) {

            $return = '<nav class="page-links" aria-label="Page links"><ul class="pagination justify-content-center">' . PHP_EOL;

            foreach ($pageLinks as $pageLink) {

                $addClass = '';

                if ($pageLink['active']) {

                    $addClass .= ' active';
                }

                if ($pageLink['disabled']) {

                    $addClass .= ' disabled';
                }

                $return .= '<li class="page-item' . $addClass . '">' . PHP_EOL;

                $return .= '<a class="page-link" href="' . $pageLink['href'] . '">' . $pageLink['txt'] . '</a>' . PHP_EOL;

                $return .= '</li>' . PHP_EOL;
            }

            $return .= '</ul></nav>' . PHP_EOL;

            return $return;
        }

        return false;
    }
}

